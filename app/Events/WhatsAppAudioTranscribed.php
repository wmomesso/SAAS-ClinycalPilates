<?php

namespace App\Events;

use App\Models\WhatsAppWebhookEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WhatsAppAudioTranscribed
{
    use Dispatchable, SerializesModels;

    public function __construct(public WhatsAppWebhookEvent $webhookEvent) {}
}
