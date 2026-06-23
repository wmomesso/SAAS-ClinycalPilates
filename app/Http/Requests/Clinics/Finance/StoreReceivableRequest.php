<?php

namespace App\Http\Requests\Clinics\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivableRequest extends FormRequest
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
        return [
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'patient_id' => 'nullable|exists:patients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'receipt_date' => 'nullable|date',
            'amount_received' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,received,partially_received,canceled',
            'notes' => 'nullable|string',
        ];
    }
}
