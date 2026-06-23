<?php

namespace Tests\Feature\Clinics\Clinic\Appointment;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $clinic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::factory()->create();
        $this->user = User::factory()->create(['clinic_id' => $this->clinic->id]);

        if (! Role::where('name', 'profissional')->exists()) {
            Role::create(['name' => 'profissional']);
        }
        $this->user->assignRole('profissional');

        $this->actingAs($this->user);
    }

    public function test_can_create_single_appointment(): void
    {
        $patient = Patient::factory()->create(['clinic_id' => $this->clinic->id]);
        $service = ServiceType::factory()->create(['clinic_id' => $this->clinic->id, 'duration_in_minutes' => 60]);
        $room = Room::factory()->create(['clinic_id' => $this->clinic->id]);

        $startTime = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);

        $response = $this->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'professional_id' => $this->user->id,
            'room_id' => $room->id,
            'service_type_id' => $service->id,
            'start_time' => $startTime->toDateTimeString(),
            'recurrence_rule' => 'none',
        ]);

        $response->assertRedirect(route('appointments.index'));
        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'start_time' => $startTime->toDateTimeString(),
        ]);
    }

    public function test_can_create_recurring_appointments_weekly(): void
    {
        $patient = Patient::factory()->create(['clinic_id' => $this->clinic->id]);
        $service = ServiceType::factory()->create(['clinic_id' => $this->clinic->id]);
        $room = Room::factory()->create(['clinic_id' => $this->clinic->id]);

        $startTime = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $until = (clone $startTime)->addWeeks(3)->toDateString();

        $response = $this->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'professional_id' => $this->user->id,
            'room_id' => $room->id,
            'service_type_id' => $service->id,
            'start_time' => $startTime->toDateTimeString(),
            'recurrence_rule' => 'weekly',
            'recurrence_until' => $until,
        ]);

        $response->assertRedirect(route('appointments.index'));

        // Deve criar 4 agendamentos (o inicial + 3 semanas)
        $this->assertEquals(4, Appointment::count());
    }

    public function test_cannot_create_appointment_with_conflict(): void
    {
        $patient = Patient::factory()->create(['clinic_id' => $this->clinic->id]);
        $service = ServiceType::factory()->create(['clinic_id' => $this->clinic->id, 'duration_in_minutes' => 60]);
        $room = Room::factory()->create(['clinic_id' => $this->clinic->id]);

        $startTime = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $endTime = (clone $startTime)->addMinutes(60);

        // Primeiro agendamento
        Appointment::create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
            'professional_id' => $this->user->id,
            'room_id' => $room->id,
            'service_type_id' => $service->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'scheduled',
        ]);

        // Tentativa de agendamento no mesmo horário
        $response = $this->post(route('appointments.store'), [
            'patient_id' => $patient->id,
            'professional_id' => $this->user->id,
            'room_id' => $room->id,
            'service_type_id' => $service->id,
            'start_time' => $startTime->toDateTimeString(),
            'recurrence_rule' => 'none',
        ]);

        $response->assertSessionHasErrors('start_time');
        $this->assertEquals(1, Appointment::count());
    }
}
