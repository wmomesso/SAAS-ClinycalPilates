<?php

namespace App\Console\Commands;

use App\Services\Transcription\WhisperCppTranscriber;
use Illuminate\Console\Command;

class WhisperCheckCommand extends Command
{
    protected $signature = 'whisper:check';

    protected $description = 'Verifica binário, FFmpeg e modelo configurados para o whisper.cpp';

    public function handle(WhisperCppTranscriber $whisper): int
    {
        $failed = false;
        $rows = [];

        foreach ($whisper->diagnostics() as $component => $diagnostic) {
            $rows[] = [$component, $diagnostic['ok'] ? 'OK' : 'AUSENTE', $diagnostic['path']];
            $failed = $failed || ! $diagnostic['ok'];
        }

        $this->table(['Componente', 'Estado', 'Caminho'], $rows);
        $this->line('Transcrição: '.((bool) config('transcription.enabled') ? 'habilitada' : 'desabilitada'));

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
