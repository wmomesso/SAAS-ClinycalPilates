<?php

namespace App\Services\Transcription;

final readonly class TranscriptionResult
{
    public function __construct(
        public string $text,
        public string $language,
        public int $processingTimeMs,
    ) {}
}
