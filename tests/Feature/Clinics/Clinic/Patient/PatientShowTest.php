<?php

namespace Tests\Feature\Clinics\Clinic\Patient;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PatientShowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Clinic $clinic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::factory()->create();
        $this->user = User::factory()->create(['clinic_id' => $this->clinic->id]);

        Role::findOrCreate('profissional');
        $this->user->assignRole('profissional');

        $this->actingAs($this->user);
    }

    public function test_patient_record_shows_standalone_appointment(): void
    {
        $patient = Patient::factory()->create([
            'clinic_id' => $this->clinic->id,
            'full_name' => 'Mariana Alves',
        ]);
        $serviceType = ServiceType::factory()->create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Pilates Avulso',
        ]);
        $room = Room::factory()->create([
            'clinic_id' => $this->clinic->id,
            'name' => 'Sala 1',
        ]);

        Appointment::factory()->create([
            'clinic_id' => $this->clinic->id,
            'patient_id' => $patient->id,
            'professional_id' => $this->user->id,
            'room_id' => $room->id,
            'service_type_id' => $serviceType->id,
            'patient_package_id' => null,
            'start_time' => now()->addDay()->setHour(9)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'status' => 'scheduled',
            'notes' => 'Agendamento experimental',
        ]);

        $response = $this->get(route('patients.show', $patient));

        $response->assertOk();
        $response->assertSee('Atendimentos Agendados');
        $response->assertSee('Pilates Avulso');
        $response->assertSee('Avulso');
        $response->assertSee('Agendamento experimental');
        $response->assertSee('Confirmar sessão');
        $response->assertSee('Falta');
        $response->assertSee('Cancelamento antecipado');
    }

    public function test_patient_health_data_can_be_updated(): void
    {
        $patient = Patient::factory()->create([
            'clinic_id' => $this->clinic->id,
            'birth_date' => '1990-05-10',
            'phone' => '(11) 99999-0000',
        ]);

        $response = $this->put(route('patients.update', $patient), [
            'full_name' => $patient->full_name,
            'birth_date' => '1990-05-10',
            'document_cpf' => $patient->document_cpf,
            'email' => 'paciente@example.com',
            'phone' => '(11) 99999-0000',
            'emergency_contact_name' => 'Contato Emergencia',
            'emergency_contact_phone' => '(11) 98888-0000',
            'medical_history' => 'Histórico de lombalgia.',
            'medications' => 'Losartana',
            'allergies' => 'Dipirona',
            'lifestyle_habits' => 'Pratica caminhada.',
            'blood_type' => 'O+',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('patients.show', $patient));
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'medical_history' => 'Histórico de lombalgia.',
            'medications' => 'Losartana',
            'allergies' => 'Dipirona',
            'lifestyle_habits' => 'Pratica caminhada.',
            'blood_type' => 'O+',
        ]);
    }
}
