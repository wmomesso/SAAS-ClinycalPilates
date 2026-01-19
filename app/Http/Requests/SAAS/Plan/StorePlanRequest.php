<?php

namespace App\Http\Requests\SAAS\Plan;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $planId = $this->route('plan') ? $this->route('plan')->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'unique:subscription_plans,slug,' . $planId],
            'stripe_plan_id' => ['required', 'string', 'unique:subscription_plans,stripe_plan_id,' . $planId],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'limit_professionals' => ['nullable', 'integer', 'min:0'],
            'limit_patients' => ['nullable', 'integer', 'min:0'],
            'limit_rooms' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
