<?php

use App\Http\Controllers\UazapiWebhookController;
use App\Jobs\SendWhatsAppMessageJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/uazapi/{secret}', UazapiWebhookController::class)
    ->where('secret', '[A-Za-z0-9_-]{43,128}')
    ->middleware('throttle:600,1')
    ->name('webhooks.uazapi');

Route::post('/test-whatsapp', function (Request $request) {
    abort_unless(app()->environment('local'), 404);

    $data = $request->validate([
        'phone' => ['required', 'string'],
        'message' => ['required', 'string'],
        'type' => ['nullable', 'in:text,buttons'],
        'buttons' => ['nullable', 'array'],
        'payload' => ['nullable', 'array'],
    ]);

    SendWhatsAppMessageJob::dispatch(
        $data['phone'],
        $data['message'],
        $data['type'] ?? 'text',
        $data['buttons'] ?? [],
        $data['payload'] ?? ['source' => 'local_test_endpoint'],
    );

    return response()->json([
        'queued' => true,
        'message' => 'WhatsApp test message queued.',
    ]);
});
