<?php

namespace App\Console\Commands;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\ClinicWhatsAppSetting;
use App\Models\WhatsAppPatientTask;
use App\Services\WhatsApp\WhatsAppPatientTaskService;
use Illuminate\Console\Command;

class PlanPatientAppointmentRemindersCommand extends Command
{
    protected $signature = 'whatsapp:plan-patient-reminders {--dry-run : Apenas informa quantos lembretes seriam criados}';

    protected $description = 'Planeja lembretes de agendamentos ainda não confirmados pelos pacientes';

    public function handle(WhatsAppPatientTaskService $tasks): int
    {
        if (! (bool) config('whatsapp.patients.enabled')) {
            $this->warn('Automação de pacientes desativada por WHATSAPP_PATIENT_AUTOMATION_ENABLED.');

            return self::SUCCESS;
        }

        $planned = 0;

        ClinicWhatsAppSetting::query()
            ->where('patient_automation_enabled', true)
            ->orderBy('clinic_id')
            ->each(function (ClinicWhatsAppSetting $settings) use ($tasks, &$planned): void {
                $stopMinutes = max($settings->reminder_stop_minutes_before, 0);
                $hoursBefore = max($settings->reminder_hours_before, 1);
                $repeatMinutes = max($settings->reminder_repeat_minutes, 1);
                $maxReminders = max($settings->reminder_max_attempts, 1);

                Appointment::query()
                    ->withoutGlobalScopes()
                    ->with(['patient.consents', 'clinic'])
                    ->where('clinic_id', $settings->clinic_id)
                    ->where('status', 'scheduled')
                    ->where('start_time', '>', now()->addMinutes($stopMinutes))
                    ->where('start_time', '<=', now()->addHours($hoursBefore))
                    ->orderBy('id')
                    ->chunkById(100, function ($appointments) use ($tasks, $repeatMinutes, $maxReminders, &$planned): void {
                        foreach ($appointments as $appointment) {
                            $patient = $appointment->patient;
                            if ($patient === null || ! $patient->is_active || ! $patient->phone || ! $patient->hasActiveWhatsAppConsent()) {
                                continue;
                            }

                            $reminders = WhatsAppPatientTask::query()
                                ->where('appointment_id', $appointment->id)
                                ->where('type', 'appointment_reminder')
                                ->orderByDesc('id')
                                ->get(['id', 'status', 'completed_at']);

                            if ($reminders->count() >= $maxReminders) {
                                continue;
                            }

                            if ($reminders->contains(fn (WhatsAppPatientTask $task): bool => in_array($task->status, [
                                WhatsAppPatientTask::STATUS_PENDING,
                                WhatsAppPatientTask::STATUS_PROCESSING,
                                WhatsAppPatientTask::STATUS_RETRYING,
                            ], true))) {
                                continue;
                            }

                            $lastSentAt = $reminders->firstWhere('status', WhatsAppPatientTask::STATUS_COMPLETED)?->completed_at;
                            if ($lastSentAt?->isAfter(now()->subMinutes($repeatMinutes))) {
                                continue;
                            }

                            $attempt = $reminders->count() + 1;
                            $planned++;
                            if ($this->option('dry-run')) {
                                continue;
                            }

                            $tasks->create([
                                'clinic_id' => $appointment->clinic_id,
                                'patient_id' => $appointment->patient_id,
                                'appointment_id' => $appointment->id,
                                'type' => 'appointment_reminder',
                                'priority' => 3,
                                'deduplication_key' => "appointment-reminder:{$appointment->id}:{$attempt}",
                                'payload' => ['reminder_number' => $attempt],
                            ]);
                        }
                    });
            });

        $suffix = $this->option('dry-run') ? ' seriam planejado(s)' : ' planejado(s)';
        $this->info("{$planned} lembrete(s){$suffix}.");

        return self::SUCCESS;
    }
}
