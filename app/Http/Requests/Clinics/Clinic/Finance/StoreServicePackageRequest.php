<?php

namespace App\Http\Requests\Clinics\Clinic\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServicePackageRequest extends FormRequest
{
    /**
     * Determina se o utilizador está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para o pacote de serviço.
     */
    public function rules(): array
    {
        $clinicId = auth()->user()->clinic_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'service_type_id' => [
                'required',
                Rule::exists('service_types', 'id')->where(function ($query) use ($clinicId) {
                    $query->where('clinic_id', $clinicId);
                }),
            ],
            'number_of_sessions' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'validity_in_days' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do pacote é obrigatório.',
            'service_type_id.required' => 'É necessário vincular o pacote a um tipo de serviço.',
            'service_type_id.exists' => 'O tipo de serviço selecionado é inválido.',
            'number_of_sessions.min' => 'O pacote deve conter pelo menos uma sessão.',
            'price.required' => 'O valor do pacote deve ser informado.',
        ];
    }
}
