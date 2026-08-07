<?php

namespace App\Jobs;

use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use InvalidArgumentException;

class SendWhatsAppMessageJob implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    /**
     * @param  array<int, array<string, mixed>>  $buttons
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $phone,
        public string $message,
        public string $type = 'text',
        public array $buttons = [],
        public array $payload = [],
    ) {}

    public function handle(WhatsAppService $whatsApp): void
    {
        match ($this->type) {
            'text' => $whatsApp->sendText($this->phone, $this->message, $this->payload),
            'buttons' => $whatsApp->sendButtons($this->phone, $this->message, $this->buttons, $this->payload),
            default => throw new InvalidArgumentException("Unsupported WhatsApp message type [{$this->type}]."),
        };
    }
}
