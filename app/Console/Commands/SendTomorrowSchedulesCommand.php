<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Clinic\ClinicAutomationService;
use Illuminate\Console\Command;

class SendTomorrowSchedulesCommand extends Command
{
    protected $signature = 'clinic:send-tomorrow-schedules';

    protected $description = 'Envia por WhatsApp a agenda do proximo dia para os profissionais.';

    public function handle(ClinicAutomationService $automation): int
    {
        // TODO: Ajustar o filtro para o papel/nome real dos profissionais da clinica.
        User::query()
            ->role('profissional')
            ->where('is_active', true)
            ->whereNotNull('clinic_id')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderBy('name')
            ->each(fn (User $professional) => $automation->sendTomorrowScheduleToProfessional($professional));

        $this->info('Agendas do proximo dia foram enfileiradas para envio.');

        return self::SUCCESS;
    }
}
