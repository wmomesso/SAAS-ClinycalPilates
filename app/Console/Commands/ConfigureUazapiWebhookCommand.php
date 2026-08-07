<?php

namespace App\Console\Commands;

use App\Models\WhatsAppIntegration;
use App\Services\WhatsApp\UazapiWebhookService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ConfigureUazapiWebhookCommand extends Command
{
    protected $signature = 'whatsapp:uazapi-webhook
        {--base-url= : URL base da instância Uazapi}
        {--instance-id= : Identificador opcional da instância}
        {--use-configured-secret : Usa WHATSAPP_WEBHOOK_SECRET para migrar um webhook existente}
        {--allow-http : Permite callback HTTP somente em ambiente local}
        {--dry-run : Valida e mostra o destino sem alterar banco ou Uazapi}';

    protected $description = 'Cria ou rotaciona o único webhook Uazapi do SaaS';

    public function handle(UazapiWebhookService $webhooks): int
    {
        $integration = WhatsAppIntegration::query()->firstOrNew([
            'provider' => 'uazapi',
        ]);

        $baseUrl = rtrim((string) ($this->option('base-url') ?: $integration->base_url ?: config('whatsapp.uazapi.base_url')), '/');
        if (! filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $this->error('Informe uma UAZAPI_BASE_URL válida.');

            return self::FAILURE;
        }

        $token = $integration->exists ? $integration->token : (string) config('whatsapp.uazapi.token');
        if ($token === '' && $this->input->isInteractive()) {
            $token = (string) $this->secret('Token da instância Uazapi');
        }
        if ($token === '') {
            $this->error('Defina UAZAPI_TOKEN ou execute o comando de forma interativa.');

            return self::FAILURE;
        }

        $appUrl = rtrim((string) config('app.url'), '/');
        $appScheme = parse_url($appUrl, PHP_URL_SCHEME);
        $allowLocalHttp = (bool) $this->option('allow-http') && app()->environment('local');
        if ($appScheme !== 'https' && ! $allowLocalHttp) {
            $this->error('APP_URL precisa usar HTTPS. Use --allow-http apenas para testes locais.');

            return self::FAILURE;
        }

        $integration->fill([
            'uuid' => $integration->uuid ?: (string) Str::uuid(),
            'base_url' => $baseUrl,
            'instance_id' => $this->option('instance-id') ?: $integration->instance_id ?: config('whatsapp.uazapi.instance_id'),
            'public_number' => $integration->public_number ?: config('whatsapp.public_number'),
            'token' => $token,
            'is_active' => true,
        ]);

        $secret = $this->webhookSecret();
        if ($secret === null) {
            return self::FAILURE;
        }
        $callbackUrl = $appUrl.'/api/webhooks/uazapi/'.$secret;
        $redactedUrl = $appUrl.'/api/webhooks/uazapi/[SEGREDO-OCULTO]';

        $this->table(['Escopo', 'Uazapi', 'Callback'], [[
            'Todo o SaaS',
            $baseUrl,
            $redactedUrl,
        ]]);

        if ($this->option('dry-run')) {
            $this->info('Validação concluída. Nenhuma alteração foi realizada.');

            return self::SUCCESS;
        }

        $response = $webhooks->register($integration, $callbackUrl);
        if (! $response->successful()) {
            $this->error("A Uazapi recusou o webhook (HTTP {$response->status()}).");
            $this->line(Str::limit($response->body(), 500));

            return self::FAILURE;
        }

        $integration->forceFill([
            'webhook_secret_hash' => hash('sha256', $secret),
            'webhook_registered_at' => now(),
        ])->save();

        $this->info('Webhook registrado com sucesso no modo simples da Uazapi.');
        $this->warn('O segredo não foi gravado em texto puro; sem --use-configured-secret, execute novamente para rotacioná-lo.');

        return self::SUCCESS;
    }

    private function webhookSecret(): ?string
    {
        if (! $this->option('use-configured-secret')) {
            return Str::random(64);
        }

        $secret = trim((string) config('whatsapp.webhook_secret'));
        if (! preg_match('/^[A-Za-z0-9_-]{43,128}$/', $secret)) {
            $this->error('WHATSAPP_WEBHOOK_SECRET deve ter entre 43 e 128 caracteres seguros.');

            return null;
        }

        $this->warn('Usando o segredo global configurado no .env.');

        return $secret;
    }
}
