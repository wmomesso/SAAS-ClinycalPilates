<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppIntegration;
use App\Models\WhatsAppMessageLog;
use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class WhatsAppService
{
    public function __construct(
        private readonly WhatsAppProviderInterface $provider,
        private readonly WhatsAppPhoneNormalizer $phones,
    ) {}

    public function sendText(string $phone, string $message, array $payload = []): array
    {
        return $this->send('text', $phone, $message, [], $payload);
    }

    public function sendButtons(string $phone, string $message, array $buttons, array $payload = []): array
    {
        return $this->send('buttons', $phone, $message, $buttons, $payload);
    }

    public function normalizePhone(string $phone): string
    {
        return $this->phones->normalize($phone) ?? '';
    }

    private function send(string $type, string $phone, string $message, array $buttons = [], array $payload = []): array
    {
        $providerName = (string) config('whatsapp.provider', 'uazapi');
        $normalizedPhone = $this->normalizePhone($phone);
        $log = WhatsAppMessageLog::create([
            'clinic_id' => $payload['clinic_id'] ?? null,
            'provider' => $providerName,
            'phone' => $this->maskPhone($normalizedPhone),
            'message' => (string) ($payload['automation'] ?? 'manual_message'),
            'payload' => array_filter([
                'type' => $type,
                'automation' => $payload['automation'] ?? null,
                'appointment_id' => $payload['appointment_id'] ?? null,
                'invoice_id' => $payload['invoice_id'] ?? null,
            ]),
            'status' => 'pending',
        ]);

        if (! (bool) config('whatsapp.enabled', true)) {
            return $this->finishLog($log, [
                'success' => false,
                'provider' => $providerName,
                'response' => null,
                'error' => 'WhatsApp sending is disabled by WHATSAPP_ENABLED.',
            ], 'failed');
        }

        if ((bool) config('whatsapp.log_only', false)) {
            return $this->finishLog($log, [
                'success' => true,
                'provider' => $providerName,
                'response' => ['log_only' => true],
                'error' => null,
            ], 'log_only');
        }

        if ($normalizedPhone === '') {
            return $this->finishLog($log, [
                'success' => false,
                'provider' => $providerName,
                'response' => null,
                'error' => 'Phone number is empty after normalization.',
            ], 'failed');
        }

        $integration = WhatsAppIntegration::query()
            ->where('provider', $providerName)
            ->where('is_active', true)
            ->first();

        if ($integration === null) {
            return $this->finishLog($log, [
                'success' => false,
                'provider' => $providerName,
                'response' => null,
                'error' => 'No active global WhatsApp integration is configured.',
            ], 'failed');
        }

        $result = match ($type) {
            'text' => $this->provider->sendText($normalizedPhone, $message, $integration),
            'buttons' => $this->provider->sendButtons($normalizedPhone, $message, $buttons, $integration),
            default => throw new InvalidArgumentException("Unsupported WhatsApp message type [{$type}]."),
        };

        return $this->finishLog($log, $result, Arr::get($result, 'success') ? 'sent' : 'failed');
    }

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 4) {
            return str_repeat('*', strlen($phone));
        }

        return str_repeat('*', strlen($phone) - 4).substr($phone, -4);
    }

    private function finishLog(WhatsAppMessageLog $log, array $result, string $status): array
    {
        $log->update([
            'provider' => $result['provider'] ?? $log->provider,
            'response' => $result['response'] ?? null,
            'status' => $status,
            'error' => $result['error'] ?? null,
        ]);

        return $result + ['log_id' => $log->id];
    }
}
