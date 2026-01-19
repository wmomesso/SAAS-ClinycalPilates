<?php

namespace App\Http\Requests\Clinics\Clinic\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determina se o utilizador está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para a criação de uma fatura.
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
            'total_amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],

            // Validação dos itens da fatura (Invoice Items)
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],

            // Campos opcionais para polimorfismo (ligação a agendamentos ou pacotes)
            'items.*.itemable_id' => ['nullable', 'integer'],
            'items.*.itemable_type' => ['nullable', 'string'],
        ];
    }

    /**
     * Mensagens de erro personalizadas para a fatura.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'É necessário selecionar um paciente para emitir a fatura.',
            'patient_id.exists' => 'O paciente selecionado é inválido ou não pertence a esta clínica.',
            'total_amount.required' => 'O valor total da fatura deve ser informado.',
            'due_date.required' => 'A data de vencimento é obrigatória.',
            'due_date.after_or_equal' => 'A data de vencimento não pode ser anterior a hoje.',
            'items.required' => 'A fatura deve conter pelo menos um serviço ou item cobrável.',
            'items.*.description.required' => 'A descrição do item é obrigatória.',
            'items.*.unit_price.numeric' => 'O preço unitário deve ser um valor numérico.',
        ];
    }
}
