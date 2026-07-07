<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => \App\Models\Clinics\Clinic\Clinic::factory(),
            'name' => fake()->word(),
            'capacity' => 1,
            'is_active' => true,
        ];
    }
}
