<?php

namespace App\Http\Requests\Clinics\Finance;

use App\Models\Clinics\Clinic\HealthInsurance\HealthInsurance;
use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReceivableRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $sourceType = $this->input('payment_source_kind') === 'health_insurance'
            ? HealthInsurance::class
            : ($this->input('payment_source_kind') === 'patient' ? Patient::class : null);

        $sourceId = match ($this->input('payment_source_kind')) {
            'health_insurance' => $this->input('health_insurance_id'),
            'patient' => $this->input('patient_id'),
            default => null,
        };

        $this->merge([
            'payment_source_type' => $sourceType,
            'payment_source_id' => $sourceId,
        ]);
    }
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
            'patient_id' => [
                'nullable',
                Rule::exists('patients', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'invoice_id' => [
                'nullable',
                Rule::exists('invoices', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'patient_package_id' => [
                'nullable',
                Rule::exists('patient_packages', 'id'),
            ],
            'payment_method_id' => [
                'nullable',
                Rule::exists('payment_methods', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'payment_source_kind' => ['nullable', Rule::in(['patient', 'health_insurance'])],
            'health_insurance_id' => [
                'nullable',
                Rule::exists('health_insurances', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'payment_source_type' => 'nullable|string',
            'payment_source_id' => 'nullable|integer',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,pix,credit_card,debit_card,bank_slip,bank_transfer,other',
            'due_date' => 'required|date',
            'receipt_date' => 'nullable|date',
            'amount_received' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,received,partially_received,canceled',
            'notes' => 'nullable|string',
        ];
    }
}
