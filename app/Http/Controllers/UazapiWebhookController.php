<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessUazapiWebhookJob;
use App\Jobs\SendWhatsAppMessageJob;
use App\Models\WhatsAppIntegration;
use App\Models\WhatsAppPhoneBinding;
use App\Models\WhatsAppWebhookEvent;
use App\Services\WhatsApp\PatientWhatsAppRequestRouter;
use App\Services\WhatsApp\UazapiWebhookParser;
use App\Services\WhatsApp\WhatsAppActivationService;
use App\Services\WhatsApp\WhatsAppPhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class UazapiWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        string $secret,
        UazapiWebhookParser $parser,
        WhatsAppPhoneNormalizer $phones,
        WhatsAppActivationService $activations,
        PatientWhatsAppRequestRouter $patientRequests,
    ): JsonResponse {
        $integration = WhatsAppIntegration::query()
            ->where('provider', 'uazapi')
            ->where('is_active', true)
            ->firstOrFail();

        abort_unless(
            $integration->webhook_secret_hash !== null
            && hash_equals($integration->webhook_secret_hash, hash('sha256', $secret)),
            404
        );

        abort_unless($request->isJson(), 415, 'O webhook aceita somente JSON.');

        $maxBytes = (int) config('whatsapp.uazapi.webhook.max_payload_bytes', 25 * 1024 * 1024);
        abort_if((int) $request->server('CONTENT_LENGTH', 0) > $maxBytes, 413);
        abort_if(strlen($request->getContent()) > $maxBytes, 413);

        $payload = $request->json()->all();
        abort_unless(is_array($payload) && $payload !== [], 422, 'Payload JSON inválido.');

        $payloadInstanceId = $parser->instanceId($payload);
        abort_if(
            $integration->instance_id !== null
            && $payloadInstanceId !== null
            && ! hash_equals($integration->instance_id, $payloadInstanceId),
            404
        );

        $sender = $parser->sender($payload);
        $normalizedPhone = $sender === null ? null : $phones->normalize($sender);
        $senderHash = $normalizedPhone === null ? null : $phones->hash($normalizedPhone);
        $binding = $senderHash === null
            ? null
            : WhatsAppPhoneBinding::query()
                ->where('phone_hash', $senderHash)
                ->where('is_active', true)
                ->first();

        $activationCode = $activations->codeFromMessage($parser->text($payload));
        $activationAttempted = $activationCode !== null
            && $normalizedPhone !== null
            && ! $parser->isFromMe($payload)
            && ! $parser->isGroup($payload);
        $activationSucceeded = false;

        if ($activationAttempted) {
            $rateLimitKey = 'whatsapp-activation:'.$senderHash;
            $maxAttempts = max((int) config('whatsapp.activation_max_attempts', 10), 1);
            $activatedBinding = null;

            if (! RateLimiter::tooManyAttempts($rateLimitKey, $maxAttempts)) {
                RateLimiter::hit(
                    $rateLimitKey,
                    (int) config('whatsapp.activation_code_ttl_minutes', 10) * 60,
                );
                $activatedBinding = $activations->activate($normalizedPhone, $activationCode);
            }

            if ($activatedBinding !== null) {
                RateLimiter::clear($rateLimitKey);
                $binding = $activatedBinding;
                $activationSucceeded = true;
            }
        }

        $status = match (true) {
            $activationSucceeded => 'activation_completed',
            $activationAttempted => 'activation_rejected',
            Str::lower($parser->eventName($payload)) === 'connection' => 'received',
            $parser->isFromMe($payload), $parser->isGroup($payload), $binding === null => 'ignored',
            default => 'received',
        };

        $webhookEvent = WhatsAppWebhookEvent::query()->firstOrCreate(
            [
                'whatsapp_integration_id' => $integration->id,
                'provider_event_id' => $parser->eventId($payload),
            ],
            [
                'clinic_id' => $binding?->clinic_id,
                'user_id' => $binding?->user_id,
                'event' => $parser->eventName($payload),
                'message_type' => $parser->messageType($payload),
                'sender_hash' => $senderHash,
                'payload' => $payload,
                'status' => $status,
                'received_at' => now(),
            ]
        );

        if ($webhookEvent->wasRecentlyCreated && $status === 'received') {
            ProcessUazapiWebhookJob::dispatch($webhookEvent->id);
        }

        if (
            $webhookEvent->wasRecentlyCreated
            && $status === 'ignored'
            && $binding === null
            && $activationCode === null
            && $normalizedPhone !== null
            && ! $parser->isFromMe($payload)
            && ! $parser->isGroup($payload)
            && $parser->text($payload) !== null
        ) {
            $patientRequests->route($webhookEvent, $normalizedPhone, $parser->text($payload));
        }

        if ($webhookEvent->wasRecentlyCreated && $activationAttempted && $normalizedPhone !== null) {
            SendWhatsAppMessageJob::dispatch(
                $normalizedPhone,
                $activationSucceeded
                    ? 'WhatsApp vinculado com sucesso. Suas automações já estão disponíveis.'
                    : 'Código inválido ou expirado. Gere um novo código em “Automação pelo WhatsApp”.',
                'text',
                [],
                array_filter([
                    'clinic_id' => $binding?->clinic_id,
                    'user_id' => $binding?->user_id,
                    'automation' => 'whatsapp_activation',
                ]),
            );
        }

        return response()->json([
            'accepted' => true,
            'duplicate' => ! $webhookEvent->wasRecentlyCreated,
        ], $webhookEvent->wasRecentlyCreated ? 202 : 200);
    }
}
