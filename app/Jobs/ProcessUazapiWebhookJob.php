<?php

namespace App\Jobs;

use App\Events\WhatsAppAudioTranscribed;
use App\Models\WhatsAppWebhookEvent;
use App\Notifications\WhatsAppIntegrationOfflineAlert;
use App\Services\Transcription\Contracts\AudioTranscriberInterface;
use App\Services\WhatsApp\UazapiMediaDownloader;
use App\Services\WhatsApp\UazapiWebhookParser;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProcessUazapiWebhookJob implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var list<int> */
    public array $backoff = [30, 120, 300];

    public function __construct(public readonly int $webhookEventId) {}

    public function handle(
        UazapiWebhookParser $parser,
        UazapiMediaDownloader $downloader,
        AudioTranscriberInterface $transcriber,
    ): void {
        $event = WhatsAppWebhookEvent::query()->with('integration')->findOrFail($this->webhookEventId);

        if (in_array($event->status, ['processed', 'transcribed', 'ignored'], true)) {
            return;
        }

        $payload = $event->payload;
        if (is_array($payload) && Str::lower($parser->eventName($payload)) === 'connection') {
            $this->processConnectionEvent($event, $payload, $parser);

            return;
        }

        if ($event->clinic_id === null || $event->user_id === null) {
            $event->update(['status' => 'ignored', 'processed_at' => now()]);

            return;
        }

        if (! is_array($payload) || $parser->isFromMe($payload) || ! $parser->isAudio($payload)) {
            $event->update([
                'status' => $parser->isFromMe($payload) ? 'ignored' : 'processed',
                'processed_at' => now(),
            ]);

            return;
        }

        if (! (bool) config('transcription.enabled')) {
            $event->update(['status' => 'awaiting_transcription']);

            return;
        }

        $event->update(['status' => 'processing', 'error' => null]);
        $disk = (string) config('transcription.disk', 'local');
        $storedPath = null;

        try {
            $audio = $downloader->audio($event->integration, $payload, $parser);
            $storedPath = sprintf(
                'private/whatsapp/%d/%s/%s.%s',
                $event->clinic_id,
                now()->format('Y/m'),
                Str::uuid(),
                $audio['extension'],
            );

            if (! Storage::disk($disk)->put($storedPath, $audio['contents'])) {
                throw new \RuntimeException('Não foi possível armazenar o áudio com segurança.');
            }

            $event->update([
                'media_path' => $storedPath,
                'media_mime' => $audio['mime'],
            ]);

            $result = $transcriber->transcribe(Storage::disk($disk)->path($storedPath));

            $event->update([
                'status' => 'transcribed',
                'transcription' => $result->text,
                'error' => null,
                'processed_at' => now(),
            ]);

            WhatsAppAudioTranscribed::dispatch($event->fresh());
        } catch (Throwable $exception) {
            $event->update([
                'status' => 'retrying',
                'error' => Str::limit($exception->getMessage(), 2000),
            ]);

            throw $exception;
        } finally {
            if ($storedPath !== null && (bool) config('transcription.delete_audio_after_transcription', true)) {
                Storage::disk($disk)->delete($storedPath);
                $event->update(['media_path' => null]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function processConnectionEvent(
        WhatsAppWebhookEvent $event,
        array $payload,
        UazapiWebhookParser $parser,
    ): void {
        $status = $parser->connectionStatus($payload);
        $alertEmail = filter_var(config('whatsapp.alert_email'), FILTER_VALIDATE_EMAIL) ?: null;

        if (
            $alertEmail !== null
            && in_array($status, ['disconnected', 'hibernated'], true)
            && Cache::add("uazapi-offline-alert:{$event->whatsapp_integration_id}:{$status}", true, now()->addMinutes(15))
        ) {
            Notification::route('mail', $alertEmail)->notify(
                new WhatsAppIntegrationOfflineAlert($event->integration, $status)
            );
        }

        $event->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        WhatsAppWebhookEvent::query()->find($this->webhookEventId)?->update([
            'status' => 'failed',
            'error' => $exception === null ? 'Falha desconhecida.' : Str::limit($exception->getMessage(), 2000),
            'processed_at' => now(),
        ]);
    }
}
