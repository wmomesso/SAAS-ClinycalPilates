<?php

namespace Database\Factories\Clinics\Clinic\Patient;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clinics\Clinic\Patient\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => \App\Models\Clinics\Clinic\Clinic::factory(),
            'full_name' => $this->faker->name(),
            'birth_date' => $this->faker->date(),
            'document_cpf' => $this->faker->numerify('###########'),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => true,
        ];
    }
}
