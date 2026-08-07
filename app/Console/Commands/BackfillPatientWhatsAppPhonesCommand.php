<?php

namespace App\Console\Commands;

use App\Models\Clinics\Clinic\Patient\Patient;
use App\Services\WhatsApp\WhatsAppPhoneNormalizer;
use Illuminate\Console\Command;

class BackfillPatientWhatsAppPhonesCommand extends Command
{
    protected $signature = 'whatsapp:backfill-patient-phones {--force : Recalcula hashes já preenchidos}';

    protected $description = 'Normaliza e indexa com HMAC os telefones dos pacientes para o roteamento do WhatsApp';

    public function handle(WhatsAppPhoneNormalizer $phones): int
    {
        $updated = 0;
        $invalid = 0;

        Patient::query()
            ->withoutGlobalScopes()
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->when(! $this->option('force'), fn ($query) => $query->whereNull('whatsapp_phone_hash'))
            ->orderBy('id')
            ->chunkById(200, function ($patients) use ($phones, &$updated, &$invalid): void {
                foreach ($patients as $patient) {
                    $normalized = $phones->normalize((string) $patient->phone);
                    if ($normalized === null) {
                        $invalid++;

                        continue;
                    }

                    $patient->forceFill(['whatsapp_phone_hash' => $phones->hash($normalized)])->saveQuietly();
                    $updated++;
                }
            });

        $this->info("{$updated} telefone(s) indexado(s); {$invalid} inválido(s) ignorado(s).");

        return self::SUCCESS;
    }
}
