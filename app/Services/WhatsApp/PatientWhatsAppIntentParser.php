<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Str;

class PatientWhatsAppIntentParser
{
    public const APPOINTMENT_CONFIRMATION = 'appointment_confirmation';

    public const CANCELLATION_REQUEST = 'cancellation_request';

    public const FINANCIAL_SUMMARY = 'financial_summary';

    public const APPOINTMENT_LIST = 'appointment_list';

    public const PACKAGE_SUMMARY = 'package_summary';

    public const HUMAN_SUPPORT = 'human_support';

    public const MENU = 'menu';

    public function parse(?string $message): ?string
    {
        if ($message === null) {
            return null;
        }

        $text = Str::lower(Str::ascii(trim($message)));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        if ($this->contains($text, ['cancelar', 'cancelamento', 'desmarcar', 'nao vou comparecer', 'nao confirmar'])) {
            return self::CANCELLATION_REQUEST;
        }

        if ($this->contains($text, ['confirmar', 'confirmo', 'confirmado', 'vou comparecer'])) {
            return self::APPOINTMENT_CONFIRMATION;
        }

        if ($this->contains($text, ['financeiro', 'fatura', 'cobranca', 'boleto', 'pix', 'pagamento', 'valor em aberto'])) {
            return self::FINANCIAL_SUMMARY;
        }

        if ($this->contains($text, ['agenda', 'minha agenda', 'proxima aula', 'proximo horario', 'meus horarios', 'agendamentos'])) {
            return self::APPOINTMENT_LIST;
        }

        if ($this->contains($text, ['pacote', 'aulas', 'meu pacote', 'saldo de aulas', 'aulas restantes', 'aulas feitas', 'reposicao', 'reposicoes'])) {
            return self::PACKAGE_SUMMARY;
        }

        if ($this->contains($text, ['atendente', 'recepcao', 'falar com a clinica', 'falar com humano', 'ajuda humana'])) {
            return self::HUMAN_SUPPORT;
        }

        if (in_array($text, ['oi', 'ola', 'bom dia', 'boa tarde', 'boa noite', 'menu', 'ajuda', 'inicio'], true)) {
            return self::MENU;
        }

        return null;
    }

    /** @param list<string> $needles */
    private function contains(string $text, array $needles): bool
    {
        return Str::contains($text, $needles);
    }
}
