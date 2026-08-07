<?php

namespace App\Console\Commands;

use App\Models\WhatsAppIntegration;
use App\Services\WhatsApp\UazapiWebhookService;
use Illuminate\Console\Command;

class UazapiWebhookStatusCommand extends Command
{
    protected $signature = 'whatsapp:uazapi-webhook-status';

    protected $description = 'Consulta o único webhook Uazapi configurado para o SaaS';

    public function handle(UazapiWebhookService $webhooks): int
    {
        $integration = WhatsAppIntegration::query()
            ->where('provider', 'uazapi')
            ->first();

        if ($integration === null) {
            $this->error('Integração global da Uazapi não encontrada.');

            return self::FAILURE;
        }

        $response = $webhooks->status($integration);
        if (! $response->successful()) {
            $this->error("Falha ao consultar a Uazapi (HTTP {$response->status()}).");

            return self::FAILURE;
        }

        $this->line((string) json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->newLine();
        $this->info('Segredo local: '.($integration->webhook_secret_hash ? 'configurado' : 'ausente'));
        $this->info('Último registro: '.($integration->webhook_registered_at?->toDateTimeString() ?? 'não registrado'));

        return self::SUCCESS;
    }
}
