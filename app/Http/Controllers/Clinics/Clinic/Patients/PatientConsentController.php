<?php

namespace App\Http\Controllers\Clinics\Clinic\Patients;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Patient\PatientConsent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientConsentController extends Controller
{
    private const TYPES = [
        'treatment',
        'data_processing',
        'whatsapp_messages',
        'image_use',
    ];

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'type' => ['required', Rule::in(self::TYPES)],
            'document_version' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'confirmation' => ['accepted'],
        ]);

        DB::transaction(function () use ($patient, $data, $request): void {
            $patient->consents()
                ->where('type', $data['type'])
                ->whereNull('revoked_at')
                ->update([
                    'revoked_at' => now(),
                    'revoked_by' => $request->user()->id,
                ]);

            $patient->consents()->create([
                'clinic_id' => $request->user()->clinic_id,
                'recorded_by' => $request->user()->id,
                'type' => $data['type'],
                'document_version' => $data['document_version'],
                'granted_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Consentimento registrado com histórico e versão.');
    }

    public function revoke(Request $request, PatientConsent $consent): RedirectResponse
    {
        $patient = $consent->patient;
        abort_unless($patient, 404);
        $this->authorize('update', $patient);

        if (! $consent->revoked_at) {
            $consent->update([
                'revoked_at' => now(),
                'revoked_by' => $request->user()->id,
            ]);
        }

        return back()->with('success', 'Consentimento revogado sem apagar o histórico.');
    }
}
