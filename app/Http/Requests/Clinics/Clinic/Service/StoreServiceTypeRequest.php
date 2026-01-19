<?php

namespace App\Http\Requests\Clinics\Clinic\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceTypeRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para o tipo de serviço.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'duration_in_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Customização das mensagens de erro (opcional).
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome do serviço é obrigatório.',
            'duration_in_minutes.required' => 'A duração é necessária para organizar a agenda.',
            'price.numeric' => 'O preço deve ser um valor numérico válido.',
        ];
    }
}
