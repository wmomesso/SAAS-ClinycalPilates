<?php

namespace App\Services\Clinic;

use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Finance\Invoice;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ClinicAutomationService
{
    // TODO: Se o dominio ganhar models dedicados, trocar User por Professional
    // e ajustar os imports/relacionamentos de Appointment, Patient e Invoice conforme os nomes reais.
    public function sendTomorrowScheduleToProfessional(User $professional): void
    {
        $tomorrow = now()->addDay();

        $appointments = Appointment::query()
            ->with(['patient', 'serviceType'])
            ->where('professional_id', $professional->id)
            ->whereDate('start_time', $tomorrow->toDateString())
            ->orderBy('start_time')
            ->get();

        $message = $appointments->isEmpty()
            ? "Agenda de amanha ({$tomorrow->format('d/m/Y')}): nenhum agendamento encontrado."
            : "Agenda de amanha ({$tomorrow->format('d/m/Y')}):\n".$appointments
                ->map(fn (Appointment $appointment) => sprintf(
                    '%s - %s%s',
                    $appointment->start_time?->format('H:i'),
                    $appointment->patient?->full_name ?? 'Paciente nao informado',
                    $appointment->serviceType?->name ? ' - '.$appointment->serviceType->name : '',
                ))
                ->implode("\n");

        $this->dispatchTo($professional->phone, $message, [
            'automation' => 'tomorrow_schedule',
            'clinic_id' => $professional->clinic_id,
            'professional_id' => $professional->id,
            'date' => $tomorrow->toDateString(),
        ]);
    }

    public function notifyProfessionalAboutCancellation(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient', 'professional']);
        $professional = $appointment->professional;

        $message = sprintf(
            "Cancelamento de agendamento:\nPaciente: %s\nData: %s\nHorario: %s",
            $appointment->patient?->full_name ?? 'Paciente nao informado',
            $appointment->start_time?->format('d/m/Y') ?? 'Data nao informada',
            $appointment->start_time?->format('H:i') ?? 'Horario nao informado',
        );

        $this->dispatchTo($professional?->phone, $message, [
            'automation' => 'appointment_cancellation',
            'clinic_id' => $appointment->clinic_id,
            'appointment_id' => $appointment->id,
        ]);
    }

    public function sendTodayBirthdaysToProfessional(User $professional): void
    {
        $today = now();
        $patients = Patient::query()
            ->where('clinic_id', $professional->clinic_id)
            ->whereMonth('birth_date', $today->month)
            ->whereDay('birth_date', $today->day)
            ->orderBy('full_name')
            ->get();

        $message = $this->formatBirthdayMessage($patients, $today);

        $this->dispatchTo($professional->phone, $message, [
            'automation' => 'today_birthdays',
            'clinic_id' => $professional->clinic_id,
            'professional_id' => $professional->id,
            'date' => $today->toDateString(),
        ]);
    }

    public function sendAppointmentConfirmationToPatient(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient', 'professional']);
        $patient = $appointment->patient;

        $message = sprintf(
            'Confirme seu agendamento em %s as %s com %s.',
            $appointment->start_time?->format('d/m/Y') ?? 'data nao informada',
            $appointment->start_time?->format('H:i') ?? 'horario nao informado',
            $appointment->professional?->name ?? 'o profissional',
        );

        SendWhatsAppMessageJob::dispatch(
            (string) $patient?->phone,
            $message,
            'buttons',
            [
                ['id' => 'confirm', 'text' => 'Confirmar'],
                ['id' => 'cancel', 'text' => 'Cancelar'],
            ],
            [
                'automation' => 'appointment_confirmation',
                'clinic_id' => $appointment->clinic_id,
                'appointment_id' => $appointment->id,
                // TODO: Conectar estes botoes a um webhook real quando o provider estiver definido.
            ],
        );
    }

    public function sendPaymentReminder(Invoice $invoice): void
    {
        $invoice->loadMissing('patient');

        $message = sprintf(
            'Lembrete de cobranca: sua fatura %s no valor de R$ %s vence em %s.',
            $invoice->invoice_number,
            number_format((float) $invoice->total_amount, 2, ',', '.'),
            $invoice->due_date?->format('d/m/Y') ?? 'data nao informada',
        );

        $this->dispatchTo($invoice->patient?->phone, $message, [
            'automation' => 'payment_reminder',
            'clinic_id' => $invoice->clinic_id,
            'invoice_id' => $invoice->id,
        ]);
    }

    public function sendOverduePaymentNotice(Invoice $invoice): void
    {
        $invoice->loadMissing('patient');

        $message = sprintf(
            'Aviso de inadimplencia: identificamos pendencia na fatura %s, vencida em %s, no valor de R$ %s.',
            $invoice->invoice_number,
            $invoice->due_date?->format('d/m/Y') ?? 'data nao informada',
            number_format((float) $invoice->total_amount - (float) $invoice->amount_paid, 2, ',', '.'),
        );

        $this->dispatchTo($invoice->patient?->phone, $message, [
            'automation' => 'overdue_payment_notice',
            'clinic_id' => $invoice->clinic_id,
            'invoice_id' => $invoice->id,
        ]);
    }

    private function dispatchTo(?string $phone, string $message, array $payload = []): void
    {
        SendWhatsAppMessageJob::dispatch((string) $phone, $message, 'text', [], $payload);
    }

    /**
     * @param  Collection<int, Patient>  $patients
     */
    private function formatBirthdayMessage(Collection $patients, Carbon $today): string
    {
        if ($patients->isEmpty()) {
            return "Aniversariantes de hoje ({$today->format('d/m/Y')}): nenhum paciente encontrado.";
        }

        return "Aniversariantes de hoje ({$today->format('d/m/Y')}):\n".$patients
            ->map(fn (Patient $patient) => sprintf(
                '%s - %s',
                $patient->full_name,
                $patient->phone,
            ))
            ->implode("\n");
    }
}
