<?php

namespace Tests\Feature\Clinics\Clinic\Patient;

use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PatientIndexTest extends TestCase
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

    public function test_can_filter_patients_by_email(): void
    {
        $matchingPatient = Patient::factory()->create([
            'clinic_id' => $this->clinic->id,
            'full_name' => 'Ana Paula Martins',
            'email' => 'ana.paula@example.com',
            'phone' => '(11) 99999-1111',
        ]);

        Patient::factory()->create([
            'clinic_id' => $this->clinic->id,
            'full_name' => 'Bruno Oliveira',
            'email' => 'bruno@example.com',
            'phone' => '(11) 98888-2222',
        ]);

        $response = $this->get(route('patients.index', ['search' => 'ana.paula']));

        $response->assertOk();
        $response->assertSee($matchingPatient->full_name);
        $response->assertSee('value="ana.paula"', false);
        $response->assertDontSee('Bruno Oliveira');
    }

    public function test_can_filter_patients_by_masked_cpf_when_cpf_has_only_digits(): void
    {
        $matchingPatient = Patient::factory()->create([
            'clinic_id' => $this->clinic->id,
            'full_name' => 'Carla Santos',
            'document_cpf' => '12345678900',
        ]);

        Patient::factory()->create([
            'clinic_id' => $this->clinic->id,
            'full_name' => 'Diego Ramos',
            'document_cpf' => '98765432100',
        ]);

        $response = $this->get(route('patients.index', ['search' => '123.456']));

        $response->assertOk();
        $response->assertSee($matchingPatient->full_name);
        $response->assertDontSee('Diego Ramos');
    }
}
