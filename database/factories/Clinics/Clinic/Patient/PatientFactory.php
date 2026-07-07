<?php

namespace Database\Factories\Clinics\Clinic\Patient;

use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'full_name' => $this->faker->name(),
            'birth_date' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => true,
        ];
    }
}
