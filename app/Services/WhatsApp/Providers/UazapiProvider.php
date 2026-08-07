<?php

namespace App\Services\WhatsApp\Providers;

use App\Models\WhatsAppIntegration;
use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Throwable;

class UazapiProvider implements WhatsAppProviderInterface
{
    public function sendText(string $phone, string $message, ?WhatsAppIntegration $integration = null): array
    {
        return $this->post('/send/text', [
            'number' => $phone,
            'text' => $message,
            'linkPreview' => false,
            'readchat' => true,
        ], $integration);
    }

    public function sendButtons(string $phone, string $message, array $buttons, ?WhatsAppIntegration $integration = null): array
    {
        return $this->post('/send/menu', [
            'number' => $phone,
            'type' => 'button',
            'text' => $message,
            'selectableCount' => 1,
            'choices' => array_values(array_map(
                static fn (array $button): string => (string) ($button['text'] ?? $button['id'] ?? ''),
                $buttons
            )),
            'readchat' => true,
        ], $integration);
    }

    private function post(string $endpoint, array $payload, ?WhatsAppIntegration $integration): array
    {
        try {
            $baseUrl = rtrim((string) ($integration?->base_url ?: config('whatsapp.uazapi.base_url')), '/');

            if ($baseUrl === '') {
                return $this->failed('Uazapi base URL is not configured.');
            }

            $response = $this->client($integration)->post($baseUrl.$endpoint, $payload);

            return [
                'success' => $response->successful(),
                'provider' => 'uazapi',
                'response' => $response->json() ?? $response->body(),
                'error' => $response->successful() ? null : $response->body(),
            ];
        } catch (Throwable $exception) {
            return $this->failed($exception->getMessage());
        }
    }

    private function client(?WhatsAppIntegration $integration): PendingRequest
    {
        $request = Http::acceptJson()->asJson();
        $token = $integration?->token ?: config('whatsapp.uazapi.token');

        if ($token) {
            $request = $request->withHeaders(['token' => $token]);
        }

        return $request;
    }

    private function failed(string $error): array
    {
        return [
            'success' => false,
            'provider' => 'uazapi',
            'response' => null,
            'error' => $error,
        ];
    }
}
