<?php

return [
    'enabled' => env('TRANSCRIPTION_ENABLED', false),
    'driver' => env('TRANSCRIPTION_DRIVER', 'whisper_cpp'),
    'disk' => env('TRANSCRIPTION_DISK', 'local'),
    'max_audio_bytes' => (int) env('TRANSCRIPTION_MAX_AUDIO_BYTES', 25 * 1024 * 1024),
    'delete_audio_after_transcription' => env('TRANSCRIPTION_DELETE_AUDIO', true),

    'ffmpeg' => [
        'binary' => env('FFMPEG_BINARY', '/usr/bin/ffmpeg'),
        'timeout' => (int) env('FFMPEG_TIMEOUT', 120),
    ],

    'whisper_cpp' => [
        'binary' => env('WHISPER_CPP_BINARY', '/opt/whisper.cpp/build/bin/whisper-cli'),
        'model' => env('WHISPER_CPP_MODEL', '/opt/whisper.cpp/models/ggml-small.bin'),
        'language' => env('WHISPER_CPP_LANGUAGE', 'pt'),
        'threads' => (int) env('WHISPER_CPP_THREADS', 4),
        'timeout' => (int) env('WHISPER_CPP_TIMEOUT', 600),
    ],
];
