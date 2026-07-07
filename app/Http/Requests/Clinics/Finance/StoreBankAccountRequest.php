<?php

namespace App\Http\Requests\Clinics\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBankAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'has_pix' => $this->boolean('has_pix'),
            'issues_bank_slips' => $this->boolean('issues_bank_slips'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'agency' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'initial_balance' => 'required|numeric',
            'has_pix' => 'boolean',
            'pix_key_type' => ['nullable', 'required_if:has_pix,1', Rule::in(['cpf', 'cnpj', 'email', 'phone', 'random'])],
            'pix_key' => 'nullable|required_if:has_pix,1|string|max:255',
            'issues_bank_slips' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
