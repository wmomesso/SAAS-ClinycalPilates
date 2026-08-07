<?php

namespace App\Console\Commands;

use App\Jobs\ProcessUazapiWebhookJob;
use App\Models\WhatsAppWebhookEvent;
use Illuminate\Console\Command;

class TranscribePendingWhatsAppAudioCommand extends Command
{
    protected $signature = 'whatsapp:transcribe-pending
        {--clinic= : Limita ao ID de uma clínica}
        {--limit=100 : Quantidade máxima a enfileirar}';

    protected $description = 'Reenfileira áudios recebidos antes da ativação do Whisper';

    public function handle(): int
    {
        if (! (bool) config('transcription.enabled')) {
            $this->error('Ative TRANSCRIPTION_ENABLED=true antes de reenfileirar.');

            return self::FAILURE;
        }

        $limit = min(max((int) $this->option('limit'), 1), 1000);
        $events = WhatsAppWebhookEvent::query()
            ->where('status', 'awaiting_transcription')
            ->when($this->option('clinic'), fn ($query, $clinic) => $query->where('clinic_id', (int) $clinic))
            ->orderBy('id')
            ->limit($limit)
            ->get(['id']);

        foreach ($events as $event) {
            ProcessUazapiWebhookJob::dispatch($event->id);
        }

        $this->info("{$events->count()} áudio(s) reenfileirado(s).");

        return self::SUCCESS;
    }
}
