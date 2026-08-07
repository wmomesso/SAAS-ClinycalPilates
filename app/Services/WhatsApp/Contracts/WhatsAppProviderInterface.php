<?php

namespace App\Services\WhatsApp\Contracts;

use App\Models\WhatsAppIntegration;

interface WhatsAppProviderInterface
{
    public function sendText(string $phone, string $message, ?WhatsAppIntegration $integration = null): array;

    public function sendButtons(string $phone, string $message, array $buttons, ?WhatsAppIntegration $integration = null): array;
}
