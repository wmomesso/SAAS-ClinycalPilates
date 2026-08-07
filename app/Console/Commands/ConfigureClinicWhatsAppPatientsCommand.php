<?php

namespace App\Console\Commands;

use App\Models\Clinics\Clinic\Clinic;
use App\Models\ClinicWhatsAppSetting;
use Illuminate\Console\Command;

class ConfigureClinicWhatsAppPatientsCommand extends Command
{
    protected $signature = 'whatsapp:configure-clinic-patients
        {clinic : ID ou subdomínio da clínica}
        {--enable : Habilita a automação de pacientes}
        {--disable : Desabilita a automação de pacientes}
        {--hours-before= : Horas de antecedência do primeiro lembrete}
        {--repeat-minutes= : Intervalo entre lembretes}
        {--max-reminders= : Limite de lembretes por agendamento}
        {--stop-minutes-before= : Para os lembretes antes do horário da aula}';

    protected $description = 'Consulta ou configura a automação de pacientes de uma clínica';

    public function handle(): int
    {
        if ($this->option('enable') && $this->option('disable')) {
            $this->error('Use somente --enable ou --disable.');

            return self::FAILURE;
        }

        $identifier = (string) $this->argument('clinic');
        $clinic = Clinic::query()
            ->where(function ($query) use ($identifier): void {
                if (ctype_digit($identifier)) {
                    $query->whereKey((int) $identifier)->orWhere('subdomain', $identifier);
                } else {
                    $query->where('subdomain', $identifier);
                }
            })
            ->first();

        if ($clinic === null) {
            $this->error('Clínica não encontrada.');

            return self::FAILURE;
        }

        $settings = ClinicWhatsAppSetting::query()->firstOrNew(['clinic_id' => $clinic->id]);
        $values = [
            'reminder_hours_before' => $this->integerOption('hours-before', $settings->reminder_hours_before ?? config('whatsapp.patients.reminder_hours_before', 24), 1, 168),
            'reminder_repeat_minutes' => $this->integerOption('repeat-minutes', $settings->reminder_repeat_minutes ?? config('whatsapp.patients.reminder_repeat_minutes', 180), 5, 10080),
            'reminder_max_attempts' => $this->integerOption('max-reminders', $settings->reminder_max_attempts ?? config('whatsapp.patients.reminder_max_attempts', 3), 1, 20),
            'reminder_stop_minutes_before' => $this->integerOption('stop-minutes-before', $settings->reminder_stop_minutes_before ?? config('whatsapp.patients.reminder_stop_minutes_before', 60), 0, 1440),
        ];

        if (in_array(null, $values, true)) {
            return self::FAILURE;
        }

        if ($this->option('enable')) {
            $values['patient_automation_enabled'] = true;
        } elseif ($this->option('disable')) {
            $values['patient_automation_enabled'] = false;
        } elseif (! $settings->exists) {
            $values['patient_automation_enabled'] = false;
        }

        $settings->fill($values)->save();

        $this->table(['Clínica', 'Ativa', 'Antecedência', 'Repetição', 'Máximo', 'Parada'], [[
            $clinic->name,
            $settings->patient_automation_enabled ? 'sim' : 'não',
            $settings->reminder_hours_before.'h',
            $settings->reminder_repeat_minutes.'min',
            $settings->reminder_max_attempts,
            $settings->reminder_stop_minutes_before.'min',
        ]]);

        return self::SUCCESS;
    }

    private function integerOption(string $name, mixed $default, int $min, int $max): ?int
    {
        $raw = $this->option($name);
        if ($raw === null) {
            return (int) $default;
        }

        if (filter_var($raw, FILTER_VALIDATE_INT) === false || (int) $raw < $min || (int) $raw > $max) {
            $this->error("--{$name} deve ser um número entre {$min} e {$max}.");

            return null;
        }

        return (int) $raw;
    }
}
