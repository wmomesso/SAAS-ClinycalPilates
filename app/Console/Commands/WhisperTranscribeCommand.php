<?php

namespace App\Console\Commands;

use App\Services\Transcription\Contracts\AudioTranscriberInterface;
use Illuminate\Console\Command;
use Throwable;

class WhisperTranscribeCommand extends Command
{
    protected $signature = 'whisper:transcribe {file : Caminho absoluto do áudio} {--language= : Idioma, por exemplo pt ou auto}';

    protected $description = 'Transcreve um arquivo para validar a instalação local do whisper.cpp';

    public function handle(AudioTranscriberInterface $transcriber): int
    {
        try {
            $result = $transcriber->transcribe(
                (string) $this->argument('file'),
                $this->option('language') ? (string) $this->option('language') : null,
            );
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->line($result->text);
        $this->newLine();
        $this->info("Idioma: {$result->language} | Processamento: {$result->processingTimeMs} ms");

        return self::SUCCESS;
    }
}
