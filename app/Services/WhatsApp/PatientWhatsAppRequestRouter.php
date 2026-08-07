<?php

namespace App\Services\WhatsApp;

use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\WhatsAppPatientTask;
use App\Models\WhatsAppWebhookEvent;
use Illuminate\Support\Facades\RateLimiter;

class PatientWhatsAppRequestRouter
{
    public function __construct(
        private readonly PatientWhatsAppIntentParser $intents,
        private readonly WhatsAppPhoneNormalizer $phones,
        private readonly WhatsAppPatientTaskService $tasks,
    ) {}

    public function route(
        WhatsAppWebhookEvent $event,
        string $normalizedPhone,
        string $message,
    ): ?WhatsAppPatientTask {
        if (! (bool) config('whatsapp.patients.enabled')) {
            return null;
        }

        $rateLimitKey = 'whatsapp-patient-request:'.$this->phones->hash($normalizedPhone);
        $maxRequests = max((int) config('whatsapp.patients.max_requests_per_ten_minutes', 30), 1);
        if (RateLimiter::tooManyAttempts($rateLimitKey, $maxRequests)) {
            return null;
        }
        RateLimiter::hit($rateLimitKey, 600);

        $intent = $this->intents->parse($message);
        if ($intent === null) {
            return null;
        }

        $phoneHash = $this->phones->hash($normalizedPhone);
        $allPatients = Patient::query()
            ->withoutGlobalScopes()
            ->with('clinic.whatsAppSettings')
            ->where('whatsapp_phone_hash', $phoneHash)
            ->where('is_active', true)
            ->get();
        $enabledPatients = $allPatients
            ->filter(fn (Patient $patient): bool => (bool) $patient->clinic?->whatsAppSettings?->patient_automation_enabled)
            ->values();

        if ($allPatients->isNotEmpty() && $enabledPatients->isEmpty()) {
            return null;
        }

        $patients = $enabledPatients
            ->filter(fn (Patient $patient): bool => $patient->hasActiveWhatsAppConsent())
            ->values();

        if ($enabledPatients->isNotEmpty() && $patients->isEmpty()) {
            return null;
        }

        $patient = $patients->count() === 1 ? $patients->first() : null;
        $appointmentId = null;

        if (in_array($intent, [
            PatientWhatsAppIntentParser::APPOINTMENT_CONFIRMATION,
            PatientWhatsAppIntentParser::CANCELLATION_REQUEST,
        ], true)) {
            $recentReminders = WhatsAppPatientTask::query()
                ->whereIn('patient_id', $patients->pluck('id'))
                ->where('type', 'appointment_reminder')
                ->where('status', WhatsAppPatientTask::STATUS_COMPLETED)
                ->where('completed_at', '>=', now()->subHours((int) config('whatsapp.patients.recent_reminder_hours', 48)))
                ->whereNotNull('appointment_id')
                ->latest('completed_at')
                ->limit(20)
                ->get()
                ->unique('appointment_id')
                ->values();

            if ($recentReminders->count() === 1) {
                $reminder = $recentReminders->first();
                $patient = $patients->firstWhere('id', $reminder->patient_id);
                $appointmentId = $reminder->appointment_id;
            } elseif ($recentReminders->count() > 1) {
                $patient = null;
            } elseif (
                $patient !== null
                && $intent === PatientWhatsAppIntentParser::APPOINTMENT_CONFIRMATION
            ) {
                $intent = 'missing_appointment_context';
            }
        }

        if ($patients->isEmpty()) {
            $intent = 'unidentified_patient';
        } elseif ($patient === null) {
            $intent = 'ambiguous_patient';
        }

        $task = $this->tasks->create([
            'clinic_id' => $patient?->clinic_id,
            'patient_id' => $patient?->id,
            'appointment_id' => $appointmentId,
            'webhook_event_id' => $event->id,
            'type' => $intent,
            'priority' => in_array($intent, [
                PatientWhatsAppIntentParser::CANCELLATION_REQUEST,
                PatientWhatsAppIntentParser::HUMAN_SUPPORT,
            ], true) ? 1 : 5,
            'payload' => [
                'phone' => $normalizedPhone,
                'message' => $message,
                'candidate_clinics' => $patients->pluck('clinic.name')->filter()->unique()->values()->all(),
            ],
        ], false);

        $event->update([
            'clinic_id' => $patient?->clinic_id,
            'patient_id' => $patient?->id,
            'status' => 'patient_task_queued',
            'processed_at' => now(),
        ]);

        return $task;
    }
}
