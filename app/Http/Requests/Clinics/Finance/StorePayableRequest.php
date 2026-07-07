<?php

namespace App\Http\Requests\Clinics\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clinicId = $this->user()->clinic_id;

        return [
            'bank_account_id' => [
                'nullable',
                Rule::exists('bank_accounts', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'description' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => [
                'nullable',
                Rule::exists('payment_methods', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'due_date' => 'required|date',
            'payment_date' => 'nullable|date',
            'amount_paid' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,paid,partially_paid,canceled',
            'notes' => 'nullable|string',
        ];
    }
}
