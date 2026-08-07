<?php

namespace App\Services\Transcription;

use App\Services\Transcription\Contracts\AudioTranscriberInterface;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Throwable;

class WhisperCppTranscriber implements AudioTranscriberInterface
{
    public function transcribe(string $audioPath, ?string $language = null): TranscriptionResult
    {
        $this->assertReady();

        $realAudioPath = realpath($audioPath);
        if ($realAudioPath === false || ! is_file($realAudioPath) || ! is_readable($realAudioPath)) {
            throw new TranscriptionException('O arquivo de áudio não existe ou não pode ser lido.');
        }

        $maxBytes = (int) config('transcription.max_audio_bytes');
        if (filesize($realAudioPath) > $maxBytes) {
            throw new TranscriptionException("O áudio excede o limite de {$maxBytes} bytes.");
        }

        $temporaryDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'clinycal-whisper-'.Str::uuid();
        if (! mkdir($temporaryDirectory, 0700, true) && ! is_dir($temporaryDirectory)) {
            throw new TranscriptionException('Não foi possível criar a área temporária da transcrição.');
        }

        $wavPath = $temporaryDirectory.DIRECTORY_SEPARATOR.'audio.wav';
        $outputPrefix = $temporaryDirectory.DIRECTORY_SEPARATOR.'transcription';
        $startedAt = hrtime(true);

        try {
            $this->convertToWav($realAudioPath, $wavPath);
            $this->runWhisper($wavPath, $outputPrefix, $language);

            $textPath = $outputPrefix.'.txt';
            if (! is_file($textPath) || ! is_readable($textPath)) {
                throw new TranscriptionException('O Whisper terminou sem gerar o arquivo de transcrição.');
            }

            $text = trim((string) file_get_contents($textPath));
            if ($text === '') {
                throw new TranscriptionException('O Whisper não identificou fala no áudio.');
            }

            return new TranscriptionResult(
                text: $text,
                language: $language ?: (string) config('transcription.whisper_cpp.language', 'pt'),
                processingTimeMs: (int) round((hrtime(true) - $startedAt) / 1_000_000),
            );
        } finally {
            $this->removeTemporaryDirectory($temporaryDirectory);
        }
    }

    /**
     * @return array<string, array{ok: bool, path: string}>
     */
    public function diagnostics(): array
    {
        $binary = (string) config('transcription.whisper_cpp.binary');
        $model = (string) config('transcription.whisper_cpp.model');
        $ffmpeg = (string) config('transcription.ffmpeg.binary');

        return [
            'ffmpeg' => ['ok' => is_file($ffmpeg) && is_executable($ffmpeg), 'path' => $ffmpeg],
            'whisper' => ['ok' => is_file($binary) && is_executable($binary), 'path' => $binary],
            'model' => ['ok' => is_file($model) && is_readable($model), 'path' => $model],
        ];
    }

    private function assertReady(): void
    {
        foreach ($this->diagnostics() as $component => $diagnostic) {
            if (! $diagnostic['ok']) {
                throw new TranscriptionException("Componente {$component} indisponível em [{$diagnostic['path']}].");
            }
        }
    }

    private function convertToWav(string $source, string $destination): void
    {
        $process = new Process([
            (string) config('transcription.ffmpeg.binary'),
            '-nostdin',
            '-hide_banner',
            '-loglevel',
            'error',
            '-i',
            $source,
            '-ar',
            '16000',
            '-ac',
            '1',
            '-c:a',
            'pcm_s16le',
            '-y',
            $destination,
        ]);
        $process->setTimeout((int) config('transcription.ffmpeg.timeout', 120));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new TranscriptionException('Falha ao converter o áudio: '.Str::limit(trim($process->getErrorOutput()), 500));
        }
    }

    private function runWhisper(string $wavPath, string $outputPrefix, ?string $language): void
    {
        $process = new Process([
            (string) config('transcription.whisper_cpp.binary'),
            '-m',
            (string) config('transcription.whisper_cpp.model'),
            '-f',
            $wavPath,
            '-l',
            $language ?: (string) config('transcription.whisper_cpp.language', 'pt'),
            '-t',
            (string) max(1, (int) config('transcription.whisper_cpp.threads', 4)),
            '-otxt',
            '-of',
            $outputPrefix,
            '-np',
            '-nt',
        ]);
        $process->setTimeout((int) config('transcription.whisper_cpp.timeout', 600));
        $process->run();

        if (! $process->isSuccessful()) {
            throw new TranscriptionException('Falha no Whisper: '.Str::limit(trim($process->getErrorOutput()), 500));
        }
    }

    private function removeTemporaryDirectory(string $directory): void
    {
        try {
            foreach (glob($directory.DIRECTORY_SEPARATOR.'*') ?: [] as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            if (is_dir($directory)) {
                rmdir($directory);
            }
        } catch (Throwable) {
            // A limpeza temporária não pode ocultar o resultado principal da transcrição.
        }
    }
}
