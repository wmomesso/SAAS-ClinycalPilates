<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppIntegration;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UazapiWebhookService
{
    public function register(WhatsAppIntegration $integration, string $callbackUrl): Response
    {
        return $this->client($integration)->post(
            rtrim($integration->base_url, '/').'/webhook',
            [
                'enabled' => true,
                'url' => $callbackUrl,
                'events' => config('whatsapp.uazapi.webhook.events', ['messages']),
                'excludeMessages' => config('whatsapp.uazapi.webhook.exclude_messages', []),
                'addUrlEvents' => false,
                'addUrlTypesMessages' => false,
            ]
        );
    }

    public function status(WhatsAppIntegration $integration): Response
    {
        return $this->client($integration)->get(rtrim($integration->base_url, '/').'/webhook');
    }

    private function client(WhatsAppIntegration $integration): \Illuminate\Http\Client\PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->withHeaders(['token' => $integration->token])
            ->timeout((int) config('whatsapp.uazapi.webhook.http_timeout', 30));
    }
}
