<?php

namespace App\Notifications;

use App\Models\WhatsAppIntegration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WhatsAppIntegrationOfflineAlert extends Notification
{
    use Queueable;

    public function __construct(
        private readonly WhatsAppIntegration $integration,
        private readonly string $status,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $number = $this->maskedNumber($this->integration->public_number);

        return (new MailMessage)
            ->subject('Alerta: WhatsApp central do SaaS indisponível')
            ->greeting('A integração do WhatsApp precisa de atenção')
            ->line("A Uazapi informou o estado [{$this->status}] para o número central do SaaS.")
            ->when($number !== null, fn (MailMessage $mail) => $mail->line("Número público: {$number}."))
            ->line('Verifique o estado da instância e a conexão do aparelho. O sistema limitará alertas repetidos por 15 minutos.');
    }

    private function maskedNumber(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number) ?: null;
        if ($digits === null) {
            return null;
        }

        return str_repeat('*', max(strlen($digits) - 4, 0)).substr($digits, -4);
    }
}
