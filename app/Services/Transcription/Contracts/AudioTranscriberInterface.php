<?php

namespace App\Services\Transcription\Contracts;

use App\Services\Transcription\TranscriptionResult;

interface AudioTranscriberInterface
{
    public function transcribe(string $audioPath, ?string $language = null): TranscriptionResult;
}
