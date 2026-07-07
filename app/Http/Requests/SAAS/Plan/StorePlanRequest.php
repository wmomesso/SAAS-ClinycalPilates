<?php

namespace App\Http\Requests\SAAS\Plan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => Str::slug($this->input('name')),
            ]);
        }

        if (! $this->has('is_active')) {
            $this->merge([
                'is_active' => false,
            ]);
        }
    }

    public function rules(): array
    {
        $planId = $this->route('plan') ? $this->route('plan')->id : null;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('subscription_plans', 'name')->ignore($planId)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('subscription_plans', 'slug')->ignore($planId)],
            'stripe_plan_id' => ['required', 'string', 'max:255', Rule::unique('subscription_plans', 'stripe_plan_id')->ignore($planId)],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_period' => ['required', Rule::in(['monthly', 'yearly'])],
            'description' => ['nullable', 'string'],
            'limit_professionals' => ['nullable', 'integer', 'min:0'],
            'limit_secretaries' => ['nullable', 'integer', 'min:0'],
            'limit_patients' => ['nullable', 'integer', 'min:0'],
            'limit_rooms' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
