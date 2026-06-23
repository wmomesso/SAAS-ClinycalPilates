<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => \App\Models\Clinics\Clinic\Clinic::factory(),
            'name' => $this->faker->word(),
            'duration_in_minutes' => 60,
            'price' => 100,
        ];
    }
}
