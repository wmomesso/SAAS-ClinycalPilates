<?php

use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use App\Services\Clinic\ClinicAutomationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function securityClinicUser(Clinic $clinic, string $role = 'profissional'): User
{
    Role::findOrCreate($role);

    return User::factory()
        ->create(['clinic_id' => $clinic->id])
        ->assignRole($role);
}

test('inactive users cannot authenticate', function () {
    $user = User::factory()->create([
        'email' => 'inactive@example.com',
        'password' => 'password',
        'is_active' => false,
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('an appointment cannot reference a patient from another clinic', function () {
    $clinic = Clinic::factory()->create(['trial_ends_at' => now()->addWeek()]);
    $otherClinic = Clinic::factory()->create(['trial_ends_at' => now()->addWeek()]);
    $professional = securityClinicUser($clinic);
    $foreignPatient = Patient::factory()->create(['clinic_id' => $otherClinic->id]);
    $room = Room::factory()->create(['clinic_id' => $clinic->id]);
    $service = ServiceType::factory()->create(['clinic_id' => $clinic->id]);

    $this->actingAs($professional)->post(route('appointments.store'), [
        'patient_id' => $foreignPatient->id,
        'professional_id' => $professional->id,
        'room_id' => $room->id,
        'service_type_id' => $service->id,
        'start_time' => now()->addDay()->toDateTimeString(),
        'recurrence_rule' => 'none',
    ])->assertSessionHasErrors('patient_id');

    $this->assertDatabaseCount('appointments', 0);
});

test('patient documents are stored only on the private disk', function () {
    Storage::fake('local');
    Storage::fake('public');

    $clinic = Clinic::factory()->create(['trial_ends_at' => now()->addWeek()]);
    $professional = securityClinicUser($clinic);
    $patient = Patient::factory()->create(['clinic_id' => $clinic->id]);

    $this->actingAs($professional)->post(route('patients.documents.store', $patient), [
        'name' => 'Exame.pdf',
        'file' => UploadedFile::fake()->create('exam.pdf', 20, 'application/pdf'),
    ])->assertSessionHasNoErrors();

    $document = $patient->documents()->firstOrFail();
    Storage::disk('local')->assertExists($document->file_path);
    Storage::disk('public')->assertMissing($document->file_path);
});

test('birthday automation never includes patients from another clinic', function () {
    Queue::fake();
    $clinic = Clinic::factory()->create();
    $otherClinic = Clinic::factory()->create();
    $professional = User::factory()->create([
        'clinic_id' => $clinic->id,
        'phone' => '11999990000',
    ]);
    Patient::factory()->create([
        'clinic_id' => $clinic->id,
        'full_name' => 'Paciente Local',
        'birth_date' => now()->subYears(30),
    ]);
    Patient::factory()->create([
        'clinic_id' => $otherClinic->id,
        'full_name' => 'Paciente de Outra Clínica',
        'birth_date' => now()->subYears(30),
    ]);

    Auth::logout();
    app(ClinicAutomationService::class)->sendTodayBirthdaysToProfessional($professional);

    Queue::assertPushed(SendWhatsAppMessageJob::class, fn (SendWhatsAppMessageJob $job) => str_contains($job->message, 'Paciente Local')
        && ! str_contains($job->message, 'Paciente de Outra Clínica'));
});
