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
        return [
            'patient_id' => [
                'required',
                Rule::exists('patients', 'id'),
            ],
            'professional_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('clinic_id', auth()->user()->clinic_id);
                }),
            ],
            'room_id' => [
                'nullable',
                Rule::exists('rooms', 'id'),
            ],
            'service_type_id' => [
                'required',
                Rule::exists('service_types', 'id'),
            ],
            'start_time' => ['required', 'date', 'after_or_equal:today'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'status' => ['nullable', 'string', 'in:scheduled,confirmed,completed,canceled,no_show'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'recurrence_rule' => ['nullable', 'string', 'in:none,daily,weekly,2x_weekly,3x_weekly'],
            'recurrence_until' => ['nullable', 'date', 'after:start_time', 'required_if:recurrence_rule,daily,weekly,2x_weekly,3x_weekly'],
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
