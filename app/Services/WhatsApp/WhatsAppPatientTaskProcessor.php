<?php

namespace App\Services\WhatsApp;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Finance\PatientPackage;
use App\Models\Clinics\Clinic\Finance\Receivable;
use App\Models\WhatsAppPatientTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WhatsAppPatientTaskProcessor
{
    public function __construct(private readonly WhatsAppService $whatsApp) {}

    public function process(WhatsAppPatientTask $task): void
    {
        $task->loadMissing(['clinic.whatsAppSettings', 'patient', 'appointment.patient', 'appointment.professional', 'appointment.serviceType']);

        if (! (bool) config('whatsapp.patients.enabled')) {
            $this->cancel($task, 'Automação de pacientes desativada globalmente.');

            return;
        }

        if ($task->clinic_id !== null && ! $task->clinic?->whatsAppSettings?->patient_automation_enabled) {
            $this->cancel($task, 'Automação de pacientes desativada para a clínica.');

            return;
        }

        if ($task->patient_id !== null && ! $task->patient?->hasActiveWhatsAppConsent()) {
            $this->cancel($task, 'Paciente sem consentimento ativo para WhatsApp.');

            return;
        }

        match ($task->type) {
            'appointment_reminder' => $this->appointmentReminder($task),
            PatientWhatsAppIntentParser::APPOINTMENT_CONFIRMATION => $this->confirmAppointment($task),
            PatientWhatsAppIntentParser::CANCELLATION_REQUEST => $this->requestCancellation($task),
            PatientWhatsAppIntentParser::FINANCIAL_SUMMARY => $this->financialSummary($task),
            PatientWhatsAppIntentParser::APPOINTMENT_LIST => $this->appointmentList($task),
            PatientWhatsAppIntentParser::PACKAGE_SUMMARY => $this->packageSummary($task),
            PatientWhatsAppIntentParser::HUMAN_SUPPORT => $this->humanSupport($task),
            PatientWhatsAppIntentParser::MENU => $this->menu($task),
            'ambiguous_patient' => $this->ambiguousPatient($task),
            'unidentified_patient' => $this->unidentifiedPatient($task),
            'missing_appointment_context' => $this->missingAppointmentContext($task),
            default => throw new RuntimeException("Tipo de tarefa WhatsApp não suportado [{$task->type}]."),
        };
    }

    private function appointmentReminder(WhatsAppPatientTask $task): void
    {
        $appointment = $task->appointment;
        if ($appointment === null || $appointment->status !== 'scheduled') {
            $this->cancel($task, 'Agendamento não está mais aguardando confirmação.');

            return;
        }

        $stopAt = now()->addMinutes($task->clinic->whatsAppSettings->reminder_stop_minutes_before);
        if ($appointment->start_time->lessThanOrEqualTo($stopAt)) {
            $this->cancel($task, 'Horário limite para lembrete atingido.');

            return;
        }

        if (! $appointment->patient?->hasActiveWhatsAppConsent()) {
            $this->cancel($task, 'Paciente sem consentimento ativo para WhatsApp.');

            return;
        }

        $clinic = $task->clinic?->name ?? 'Sua clínica';
        $message = sprintf(
            "%s\n\nOlá, %s. Confirme sua aula em %s às %s%s.",
            $clinic,
            $this->firstName((string) $appointment->patient?->full_name),
            $appointment->start_time->format('d/m/Y'),
            $appointment->start_time->format('H:i'),
            $appointment->professional?->name ? ' com '.$appointment->professional->name : '',
        );

        $this->send($task, $message, [
            ['id' => 'confirm', 'text' => 'Confirmar'],
            ['id' => 'cancel', 'text' => 'Cancelar'],
        ]);
        $this->complete($task, ['appointment_status' => $appointment->status]);
    }

    private function confirmAppointment(WhatsAppPatientTask $task): void
    {
        $appointment = DB::transaction(function () use ($task): ?Appointment {
            $appointment = Appointment::query()->withoutGlobalScopes()->lockForUpdate()->find($task->appointment_id);
            if ($appointment?->status === 'scheduled') {
                $appointment->update(['status' => 'confirmed']);
            }

            return $appointment;
        });

        if ($appointment === null) {
            $this->send($task, $this->clinicPrefix($task).'Não encontramos o agendamento relacionado a essa confirmação.');
        } elseif ($appointment->status === 'confirmed') {
            $this->send($task, $this->clinicPrefix($task).sprintf(
                'Presença confirmada para %s às %s. Obrigado!',
                $appointment->start_time->format('d/m/Y'),
                $appointment->start_time->format('H:i'),
            ));
        } else {
            $this->send($task, $this->clinicPrefix($task).'Esse agendamento não está mais disponível para confirmação.');
        }

        $this->complete($task, ['appointment_status' => $appointment?->status]);
    }

    private function requestCancellation(WhatsAppPatientTask $task): void
    {
        $appointment = $task->appointment;
        $details = $appointment === null
            ? ''
            : sprintf(' de %s às %s', $appointment->start_time->format('d/m/Y'), $appointment->start_time->format('H:i'));

        $this->send(
            $task,
            $this->clinicPrefix($task)."Sua solicitação de cancelamento{$details} foi registrada e será analisada pela clínica.",
        );
        $this->awaitStaff($task, ['requested_action' => 'cancel_appointment']);
    }

    private function financialSummary(WhatsAppPatientTask $task): void
    {
        $receivables = Receivable::query()
            ->withoutGlobalScopes()
            ->where('clinic_id', $task->clinic_id)
            ->where('patient_id', $task->patient_id)
            ->whereIn('status', ['pending', 'partially_received'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        if ($receivables->isEmpty()) {
            $message = $this->clinicPrefix($task).'Não encontramos cobranças em aberto no momento.';
        } else {
            $items = $receivables->map(function (Receivable $receivable): string {
                $balance = max(0, (float) $receivable->amount - (float) $receivable->amount_received);

                return sprintf(
                    '%s — R$ %s — vence em %s',
                    Str::limit($receivable->description, 50),
                    number_format($balance, 2, ',', '.'),
                    $receivable->due_date->format('d/m/Y'),
                );
            })->implode("\n");

            $message = $this->clinicPrefix($task)."Pendências financeiras:\n{$items}\n\nPara pagamento ou negociação, responda “atendente”.";
        }

        $this->send($task, $message);
        $this->complete($task, ['items' => $receivables->count()]);
    }

    private function appointmentList(WhatsAppPatientTask $task): void
    {
        $appointments = Appointment::query()
            ->withoutGlobalScopes()
            ->where('clinic_id', $task->clinic_id)
            ->where('patient_id', $task->patient_id)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $message = $appointments->isEmpty()
            ? $this->clinicPrefix($task).'Você não possui horários futuros agendados.'
            : $this->clinicPrefix($task)."Próximos horários:\n".$appointments->map(
                fn (Appointment $appointment): string => sprintf(
                    '%s às %s — %s',
                    $appointment->start_time->format('d/m/Y'),
                    $appointment->start_time->format('H:i'),
                    $appointment->status === 'confirmed' ? 'confirmado' : 'aguardando confirmação',
                )
            )->implode("\n");

        $this->send($task, $message);
        $this->complete($task, ['items' => $appointments->count()]);
    }

    private function packageSummary(WhatsAppPatientTask $task): void
    {
        $packages = PatientPackage::query()
            ->with('package')
            ->where('patient_id', $task->patient_id)
            ->where('status', 'active')
            ->orderByDesc('start_date')
            ->limit(3)
            ->get();

        if ($packages->isEmpty()) {
            $message = $this->clinicPrefix($task).'Não encontramos pacote ativo no momento.';
        } else {
            $message = $this->clinicPrefix($task)."Resumo das aulas:\n".$packages->map(
                fn (PatientPackage $package): string => sprintf(
                    "%s\nContratadas: %d | Realizadas: %d | Faltas: %d | Restantes: %d%s",
                    $package->package?->name ?? 'Pacote',
                    $package->total_sessions,
                    $package->used_sessions,
                    $package->missed_sessions,
                    $package->remaining_sessions,
                    $package->end_date ? ' | Validade: '.$package->end_date->format('d/m/Y') : '',
                )
            )->implode("\n\n");
        }

        $this->send($task, $message);
        $this->complete($task, ['items' => $packages->count()]);
    }

    private function humanSupport(WhatsAppPatientTask $task): void
    {
        $this->send($task, $this->clinicPrefix($task).'Seu pedido de atendimento foi registrado. A equipe da clínica responderá assim que possível.');
        $this->awaitStaff($task, ['requested_action' => 'human_support']);
    }

    private function menu(WhatsAppPatientTask $task): void
    {
        $this->send($task, $this->clinicPrefix($task)."Como podemos ajudar?\n\n• Minha agenda\n• Confirmar ou cancelar\n• Meu pacote e aulas\n• Financeiro\n• Falar com a clínica");
        $this->complete($task);
    }

    private function ambiguousPatient(WhatsAppPatientTask $task): void
    {
        $clinics = collect($task->payload['candidate_clinics'] ?? [])->implode(', ');
        $suffix = $clinics === '' ? '' : " Cadastros encontrados: {$clinics}.";
        $this->send($task, "Seu telefone está associado a mais de um cadastro.{$suffix} Envie “atendente” para que a clínica confirme com segurança.");
        $this->complete($task, ['reason' => 'ambiguous_patient']);
    }

    private function unidentifiedPatient(WhatsAppPatientTask $task): void
    {
        $this->send($task, 'Não localizamos um cadastro ativo para este telefone. Entre em contato com a recepção da sua clínica para atualizar seus dados.');
        $this->complete($task, ['reason' => 'unidentified_patient']);
    }

    private function missingAppointmentContext(WhatsAppPatientTask $task): void
    {
        $this->send($task, $this->clinicPrefix($task).'Não encontramos um lembrete recente relacionado a essa resposta. Consulte “minha agenda” ou fale com a clínica.');
        $this->complete($task, ['reason' => 'missing_appointment_context']);
    }

    /** @param list<array<string, mixed>> $buttons */
    private function send(WhatsAppPatientTask $task, string $message, array $buttons = []): void
    {
        $phone = (string) ($task->patient?->phone ?: ($task->payload['phone'] ?? ''));
        $payload = array_filter([
            'clinic_id' => $task->clinic_id,
            'patient_id' => $task->patient_id,
            'appointment_id' => $task->appointment_id,
            'automation' => $task->type,
        ]);
        $result = $buttons === []
            ? $this->whatsApp->sendText($phone, $message, $payload)
            : $this->whatsApp->sendButtons($phone, $message, $buttons, $payload);

        if (! ($result['success'] ?? false)) {
            throw new RuntimeException((string) ($result['error'] ?? 'Falha ao enviar a resposta pelo WhatsApp.'));
        }
    }

    /** @param array<string, mixed> $result */
    private function complete(WhatsAppPatientTask $task, array $result = []): void
    {
        $task->update([
            'status' => WhatsAppPatientTask::STATUS_COMPLETED,
            'result' => $result,
            'completed_at' => now(),
            'last_error' => null,
        ]);
    }

    /** @param array<string, mixed> $result */
    private function awaitStaff(WhatsAppPatientTask $task, array $result = []): void
    {
        $task->update([
            'status' => WhatsAppPatientTask::STATUS_AWAITING_STAFF,
            'result' => $result,
            'completed_at' => now(),
            'last_error' => null,
        ]);
    }

    private function cancel(WhatsAppPatientTask $task, string $reason): void
    {
        $task->update([
            'status' => WhatsAppPatientTask::STATUS_CANCELED,
            'result' => ['reason' => $reason],
            'completed_at' => now(),
        ]);
    }

    private function clinicPrefix(WhatsAppPatientTask $task): string
    {
        return ($task->clinic?->name ?? 'Sua clínica')."\n\n";
    }

    private function firstName(string $name): string
    {
        return Str::before(trim($name), ' ') ?: 'paciente';
    }
}
