<?php

namespace App\Http\Requests\Clinics\Clinic\Patient;

use App\Rules\Cpf;
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
                new Cpf,
                Rule::unique('patients')->ignore($this->patient),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'array'],
            'medical_history' => ['nullable', 'string'],
        ];
    }
}
