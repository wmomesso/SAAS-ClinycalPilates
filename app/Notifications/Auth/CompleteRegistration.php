<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CompleteRegistration extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $url,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Complete seu cadastro')
            ->greeting('Vamos finalizar seu acesso')
            ->line('Recebemos uma solicitação para iniciar o teste grátis do Clinycal Pilates com este e-mail.')
            ->line('Clique no botão abaixo para confirmar o e-mail, criar sua senha e completar os dados da clínica.')
            ->action('Completar cadastro', $this->url)
            ->line('Este link expira em 60 minutos. Se você não solicitou este acesso, ignore este e-mail.');
    }
}
