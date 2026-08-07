<?php

namespace App\Observers;

use App\Models\Clinics\Clinic\Patient\Patient;
use App\Services\WhatsApp\WhatsAppPhoneNormalizer;

class PatientWhatsAppPhoneObserver
{
    public function __construct(private readonly WhatsAppPhoneNormalizer $phones) {}

    public function saving(Patient $patient): void
    {
        if (! $patient->isDirty('phone') && $patient->whatsapp_phone_hash !== null) {
            return;
        }

        $normalized = $this->phones->normalize((string) $patient->phone);
        $patient->setAttribute(
            'whatsapp_phone_hash',
            $normalized === null ? null : $this->phones->hash($normalized),
        );
    }
}
