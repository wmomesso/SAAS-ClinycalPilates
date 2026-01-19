<?php

namespace App\Http\Requests\Clinics\Clinic\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // A autorização é tratada pela PatientPolicy no Controller
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'document_cpf' => [
                'nullable',
                'string',
                Rule::unique('patients')->ignore($this->patient)->where(fn ($q) => $q->where('clinic_id', auth()->user()->clinic_id))
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'array'],
            'medical_history' => ['nullable', 'string'],
        ];
    }
}
