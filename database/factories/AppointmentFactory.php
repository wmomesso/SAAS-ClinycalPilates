<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => \App\Models\Clinics\Clinic\Clinic::factory(),
            'patient_id' => \App\Models\Clinics\Clinic\Patient\Patient::factory(),
            'professional_id' => \App\Models\User::factory(),
            'room_id' => \App\Models\Clinics\Clinic\Room\Room::factory(),
            'service_type_id' => \App\Models\Clinics\Clinic\Services\ServiceType::factory(),
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(2),
            'status' => 'scheduled',
        ];
    }
}
