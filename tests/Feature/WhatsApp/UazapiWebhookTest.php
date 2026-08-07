<?php

use App\Jobs\ProcessUazapiWebhookJob;
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\User;
use App\Models\WhatsAppIntegration;
use App\Models\WhatsAppPhoneBinding;
use App\Models\WhatsAppWebhookEvent;
use App\Notifications\WhatsAppIntegrationOfflineAlert;
use App\Services\Transcription\Contracts\AudioTranscriberInterface;
use App\Services\Transcription\TranscriptionResult;
use App\Services\WhatsApp\WhatsAppActivationService;
use App\Services\WhatsApp\WhatsAppPhoneNormalizer;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

function globalUazapiIntegration(string $secret = 'test-secret-with-at-least-forty-three-characters-123'): WhatsAppIntegration
{
    return WhatsAppIntegration::query()->create([
        'provider' => 'uazapi',
        'base_url' => 'https://uazapi.example',
        'instance_id' => 'saas-instance',
        'public_number' => '5511888888888',
        'token' => 'single-saas-token',
        'webhook_secret_hash' => hash('sha256', $secret),
        'is_active' => true,
    ]);
}

function linkedUser(Clinic $clinic, string $phone): User
{
    $user = User::factory()->create(['clinic_id' => $clinic->id]);
    $phones = app(WhatsAppPhoneNormalizer::class);
    WhatsAppPhoneBinding::query()->create([
        'user_id' => $user->id,
        'clinic_id' => $clinic->id,
        'phone' => $phone,
        'phone_hash' => $phones->hash($phone),
        'is_active' => true,
        'bound_at' => now(),
    ]);

    return $user;
}

function uazapiAudioPayload(string $messageId = 'MESSAGE-001', string $phone = '5511999999999'): array
{
    return [
        'event' => 'messages',
        'instance' => ['id' => 'saas-instance'],
        'type' => 'message',
        'message' => [
            'chatid' => $phone.'@s.whatsapp.net',
            'sender' => '123456789012345@lid',
            'messageid' => $messageId,
            'fromMe' => false,
            'type' => 'AudioMessage',
            'content' => ['mimetype' => 'audio/ogg; codecs=opus'],
        ],
    ];
}

function uazapiTextPayload(string $text, string $phone, string $messageId): array
{
    return [
        'event' => 'messages',
        'instance' => ['id' => 'saas-instance'],
        'message' => [
            'chatid' => $phone.'@s.whatsapp.net',
            'sender' => '123456789012345@lid',
            'messageid' => $messageId,
            'fromMe' => false,
            'type' => 'Conversation',
            'content' => $text,
        ],
    ];
}

function testWavContents(): string
{
    $pcm = str_repeat("\0", 320);
    $dataSize = strlen($pcm);

    return 'RIFF'.pack('V', 36 + $dataSize).'WAVEfmt '.pack('VvvVVvv', 16, 1, 1, 16000, 32000, 2, 16)
        .'data'.pack('V', $dataSize).$pcm;
}

test('the single uazapi webhook routes an encrypted idempotent event by the bound sender', function () {
    Queue::fake();
    $secret = 'test-secret-with-at-least-forty-three-characters-123';
    $integration = globalUazapiIntegration($secret);
    $clinic = Clinic::factory()->create();
    $user = linkedUser($clinic, '5511999999999');
    $route = route('webhooks.uazapi', [$secret]);

    $this->postJson(route('webhooks.uazapi', [str_repeat('x', 50)]), uazapiAudioPayload())->assertNotFound();
    $this->postJson($route, uazapiAudioPayload())->assertAccepted()->assertJson(['duplicate' => false]);
    $this->postJson($route, uazapiAudioPayload())->assertOk()->assertJson(['duplicate' => true]);

    $event = WhatsAppWebhookEvent::query()->sole();
    expect($event->whatsapp_integration_id)->toBe($integration->id)
        ->and($event->clinic_id)->toBe($clinic->id)
        ->and($event->user_id)->toBe($user->id)
        ->and($event->payload['message']['chatid'])->toBe('5511999999999@s.whatsapp.net');

    expect((string) DB::table('whatsapp_webhook_events')->value('payload'))->not->toContain('5511999999999');
    Queue::assertPushed(ProcessUazapiWebhookJob::class, 1);
});

test('temporary codes bind each sender to the correct user and clinic through the same webhook', function () {
    Queue::fake();
    $secret = 'test-secret-with-at-least-forty-three-characters-123';
    globalUazapiIntegration($secret);
    $firstClinic = Clinic::factory()->create();
    $secondClinic = Clinic::factory()->create();
    $first = User::factory()->create(['clinic_id' => $firstClinic->id]);
    $second = User::factory()->create(['clinic_id' => $secondClinic->id]);
    $activations = app(WhatsAppActivationService::class);

    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload($activations->generate($first), '5511911111111', 'ACT-1'))->assertAccepted();
    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload($activations->generate($second), '5511922222222', 'ACT-2'))->assertAccepted();

    expect(WhatsAppPhoneBinding::query()->where('user_id', $first->id)->sole()->clinic_id)->toBe($firstClinic->id)
        ->and(WhatsAppPhoneBinding::query()->where('user_id', $second->id)->sole()->clinic_id)->toBe($secondClinic->id)
        ->and(WhatsAppIntegration::query()->count())->toBe(1)
        ->and(WhatsAppWebhookEvent::query()->where('status', 'activation_completed')->count())->toBe(2);
    Queue::assertPushed(SendWhatsAppMessageJob::class, 2);
    Queue::assertNotPushed(ProcessUazapiWebhookJob::class);
});

test('an activation code is disposable and an unbound sender cannot run automations', function () {
    Queue::fake();
    $secret = 'test-secret-with-at-least-forty-three-characters-123';
    globalUazapiIntegration($secret);
    $clinic = Clinic::factory()->create();
    $user = User::factory()->create(['clinic_id' => $clinic->id]);
    $code = app(WhatsAppActivationService::class)->generate($user);

    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload($code, '5511911111111', 'ACT-1'))->assertAccepted();
    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload($code, '5511922222222', 'ACT-2'))->assertAccepted();
    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload('registrar despesa', '5511933333333', 'MSG-3'))->assertAccepted();

    expect(WhatsAppPhoneBinding::query()->count())->toBe(1)
        ->and(WhatsAppWebhookEvent::query()->where('provider_event_id', 'ACT-2')->value('status'))->toBe('activation_rejected')
        ->and(WhatsAppWebhookEvent::query()->where('provider_event_id', 'MSG-3')->value('status'))->toBe('ignored');
    Queue::assertNotPushed(ProcessUazapiWebhookJob::class);
});

test('activation attempts are limited per sender without throttling other clinics', function () {
    Queue::fake();
    config()->set('whatsapp.activation_max_attempts', 2);
    $secret = 'test-secret-with-at-least-forty-three-characters-123';
    globalUazapiIntegration($secret);
    $clinic = Clinic::factory()->create();
    $user = User::factory()->create(['clinic_id' => $clinic->id]);
    $code = app(WhatsAppActivationService::class)->generate($user);
    $invalidCodes = collect(['FIN-000000', 'FIN-000001', 'FIN-000002'])
        ->reject(fn (string $candidate) => $candidate === $code)
        ->values();

    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload($invalidCodes[0], '5511944444444', 'TRY-1'))->assertAccepted();
    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload($invalidCodes[1], '5511944444444', 'TRY-2'))->assertAccepted();
    $this->postJson(route('webhooks.uazapi', [$secret]), uazapiTextPayload($code, '5511944444444', 'TRY-3'))->assertAccepted();

    expect(WhatsAppPhoneBinding::query()->count())->toBe(0)
        ->and(WhatsAppWebhookEvent::query()->where('status', 'activation_rejected')->count())->toBe(3);
});

test('a webhook cannot claim another uazapi instance', function () {
    Queue::fake();
    $secret = 'test-secret-with-at-least-forty-three-characters-123';
    globalUazapiIntegration($secret);
    $payload = uazapiAudioPayload();
    $payload['instance']['id'] = 'another-instance';

    $this->postJson(route('webhooks.uazapi', [$secret]), $payload)->assertNotFound();
    expect(WhatsAppWebhookEvent::query()->count())->toBe(0);
});

test('audio from a bound professional is downloaded and transcribed with the global token', function () {
    Storage::fake('local');
    config()->set('transcription.enabled', true);
    config()->set('transcription.disk', 'local');
    config()->set('transcription.delete_audio_after_transcription', true);
    Http::fake(['https://uazapi.example/message/download' => Http::response(testWavContents(), 200, ['Content-Type' => 'audio/wav'])]);
    app()->bind(AudioTranscriberInterface::class, fn () => new class implements AudioTranscriberInterface
    {
        public function transcribe(string $audioPath, ?string $language = null): TranscriptionResult
        {
            expect(is_file($audioPath))->toBeTrue();

            return new TranscriptionResult('Preciso remarcar meu pilates.', 'pt', 25);
        }
    });

    $integration = globalUazapiIntegration();
    $clinic = Clinic::factory()->create();
    $user = linkedUser($clinic, '5511999999999');
    $event = WhatsAppWebhookEvent::query()->create([
        'clinic_id' => $clinic->id,
        'user_id' => $user->id,
        'whatsapp_integration_id' => $integration->id,
        'provider_event_id' => 'AUDIO-001',
        'event' => 'messages',
        'message_type' => 'audiomessage',
        'payload' => uazapiAudioPayload('AUDIO-001'),
        'status' => 'received',
    ]);

    app()->call([new ProcessUazapiWebhookJob($event->id), 'handle']);

    expect($event->refresh()->status)->toBe('transcribed')
        ->and($event->transcription)->toBe('Preciso remarcar meu pilates.')
        ->and((string) DB::table('whatsapp_webhook_events')->where('id', $event->id)->value('transcription'))->not->toContain('remarcar meu pilates');
    Http::assertSent(fn ($request) => $request->hasHeader('token', 'single-saas-token') && $request['id'] === 'AUDIO-001');
});

test('the artisan command registers one global webhook without storing plaintext secrets', function () {
    config()->set('app.url', 'https://clinicas.example');
    config()->set('whatsapp.uazapi.base_url', 'https://uazapi.example');
    config()->set('whatsapp.uazapi.token', 'super-secret-token');
    Http::fake(['https://uazapi.example/webhook' => Http::response(['ok' => true])]);

    $this->artisan('whatsapp:uazapi-webhook')->assertSuccessful();

    $integration = WhatsAppIntegration::query()->sole();
    expect($integration->token)->toBe('super-secret-token')
        ->and($integration->webhook_secret_hash)->toHaveLength(64)
        ->and((string) DB::table('whatsapp_integrations')->value('token'))->not->toContain('super-secret-token');
    Http::assertSent(fn ($request) => $request->hasHeader('token', 'super-secret-token')
        && str_contains((string) $request['url'], '/api/webhooks/uazapi/')
        && ! str_contains((string) $request['url'], $integration->uuid.'/')
        && $request['events'] === ['messages', 'messages_update', 'connection']);
});

test('an existing global webhook secret can be imported explicitly', function () {
    $configuredSecret = str_repeat('a1', 32);
    config()->set('app.url', 'https://clinicas.example');
    config()->set('whatsapp.uazapi.base_url', 'https://uazapi.example');
    config()->set('whatsapp.uazapi.token', 'secret-token');
    config()->set('whatsapp.webhook_secret', $configuredSecret);
    Http::fake(['https://uazapi.example/webhook' => Http::response(['ok' => true])]);

    $this->artisan('whatsapp:uazapi-webhook', ['--use-configured-secret' => true])->assertSuccessful();

    expect(WhatsAppIntegration::query()->sole()->webhook_secret_hash)->toBe(hash('sha256', $configuredSecret));
    Http::assertSent(fn ($request) => str_ends_with((string) $request['url'], '/'.$configuredSecret));
});

test('offline connection events send one central operational alert', function () {
    Notification::fake();
    config()->set('whatsapp.alert_email', 'operacao@example.com');
    $integration = globalUazapiIntegration();
    $event = WhatsAppWebhookEvent::query()->create([
        'whatsapp_integration_id' => $integration->id,
        'provider_event_id' => 'CONNECTION-001',
        'event' => 'connection',
        'payload' => ['event' => 'connection', 'instance' => ['id' => 'saas-instance', 'status' => 'disconnected']],
        'status' => 'received',
    ]);

    app()->call([new ProcessUazapiWebhookJob($event->id), 'handle']);

    expect($event->refresh()->status)->toBe('processed');
    Notification::assertSentOnDemand(WhatsAppIntegrationOfflineAlert::class);
});

test('outbound messages from every clinic use the single saas uazapi token', function () {
    config()->set('whatsapp.enabled', true);
    config()->set('whatsapp.log_only', false);
    config()->set('whatsapp.provider', 'uazapi');
    Http::fake(['https://uazapi.example/send/text' => Http::response(['ok' => true])]);
    globalUazapiIntegration();
    $firstClinic = Clinic::factory()->create();
    $secondClinic = Clinic::factory()->create();

    app(WhatsAppService::class)->sendText('11999999999', 'Mensagem A', ['clinic_id' => $firstClinic->id]);
    app(WhatsAppService::class)->sendText('11888888888', 'Mensagem B', ['clinic_id' => $secondClinic->id]);

    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request->hasHeader('token', 'single-saas-token'));
    expect(WhatsAppIntegration::query()->count())->toBe(1);
});

test('brazilian local phones are normalized even when the area code starts with 55', function () {
    expect(app(WhatsAppPhoneNormalizer::class)->normalize('(55) 99999-9999'))
        ->toBe('5555999999999');
});
