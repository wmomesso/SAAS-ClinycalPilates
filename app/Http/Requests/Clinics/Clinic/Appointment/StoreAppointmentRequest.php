<?php

namespace App\Http\Requests\Clinics\Clinic\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para o agendamento.
     */
    public function rules(): array
    {
        $clinicId = auth()->user()->clinic_id;

        return [
            'patient_id' => [
                'required',
                Rule::exists('patients', 'id')->where(function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                }),
            ],
            'professional_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                }),
            ],
            'room_id' => [
                'nullable',
                Rule::exists('rooms', 'id')->where(function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                }),
            ],
            'service_type_id' => [
                'required',
                Rule::exists('service_types', 'id')->where(function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                }),
            ],
            'start_time' => ['required', 'date', 'after:today'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'status' => ['nullable', 'string', 'in:scheduled,confirmed,completed,canceled,no_show'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'recurrence_rule' => ['nullable', 'string'],
        ];
    }

    /**
     * Mensagens amigáveis para erros de validação.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Você precisa selecionar um paciente.',
            'professional_id.required' => 'Um profissional deve ser designado para o atendimento.',
            'start_time.after' => 'A data do agendamento não pode ser no passado.',
            'service_type_id.required' => 'O tipo de serviço é obrigatório.',
        ];
    }
}
