<?php

use App\Http\Middleware\EnsureClinicHasActiveSubscription;
use App\Jobs\ProcessWhatsAppPatientTaskJob;
use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Finance\Receivable;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\ClinicWhatsAppSetting;
use App\Models\User;
use App\Models\WhatsAppIntegration;
use App\Models\WhatsAppPatientTask;
use App\Services\WhatsApp\WhatsAppPatientTaskService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;

function patientTaskIntegration(string $secret = 'patient-task-secret-with-at-least-forty-three-characters'): WhatsAppIntegration
{
    return WhatsAppIntegration::query()->create([
        'provider' => 'uazapi',
        'base_url' => 'https://uazapi.example',
        'instance_id' => 'patient-task-instance',
        'public_number' => '5511888888888',
        'token' => 'patient-task-token',
        'webhook_secret_hash' => hash('sha256', $secret),
        'is_active' => true,
    ]);
}

function patientTaskPayload(string $text, string $phone, string $id): array
{
    return [
        'event' => 'messages',
        'instance' => ['id' => 'patient-task-instance'],
        'message' => [
            'chatid' => $phone.'@s.whatsapp.net',
            'messageid' => $id,
            'fromMe' => false,
            'type' => 'Conversation',
            'content' => $text,
        ],
    ];
}

function enablePatientAutomation(Clinic $clinic, array $attributes = []): ClinicWhatsAppSetting
{
    return ClinicWhatsAppSetting::query()->create(array_merge([
        'clinic_id' => $clinic->id,
        'patient_automation_enabled' => true,
        'reminder_hours_before' => 24,
        'reminder_repeat_minutes' => 180,
        'reminder_max_attempts' => 3,
        'reminder_stop_minutes_before' => 60,
    ], $attributes));
}

function patientWithWhatsApp(Clinic $clinic, string $phone = '5511977777777', bool $consent = true): Patient
{
    $patient = Patient::factory()->create([
        'clinic_id' => $clinic->id,
        'phone' => $phone,
        'is_active' => true,
    ]);

    if ($consent) {
        $patient->consents()->create([
            'clinic_id' => $clinic->id,
            'type' => 'whatsapp_messages',
            'document_version' => '1.0',
            'granted_at' => now(),
        ]);
    }

    return $patient;
}

function patientAppointment(Clinic $clinic, Patient $patient, array $attributes = []): Appointment
{
    $professional = User::factory()->create(['clinic_id' => $clinic->id]);

    return Appointment::factory()->create(array_merge([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'professional_id' => $professional->id,
        'start_time' => now()->addHours(12),
        'end_time' => now()->addHours(13),
        'status' => 'scheduled',
    ], $attributes));
}

test('a recognized patient request is persisted without dispatching or processing in the webhook', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    $secret = 'patient-task-secret-with-at-least-forty-three-characters';
    patientTaskIntegration($secret);
    $clinic = Clinic::factory()->create();
    enablePatientAutomation($clinic);
    $patient = patientWithWhatsApp($clinic);

    $this->postJson(
        route('webhooks.uazapi', [$secret]),
        patientTaskPayload('Quero consultar meu financeiro', '5511977777777', 'PATIENT-FINANCE-1'),
    )->assertAccepted();

    $task = WhatsAppPatientTask::query()->sole();
    expect($task->type)->toBe('financial_summary')
        ->and($task->status)->toBe(WhatsAppPatientTask::STATUS_PENDING)
        ->and($task->clinic_id)->toBe($clinic->id)
        ->and($task->patient_id)->toBe($patient->id)
        ->and($task->webhookEvent->status)->toBe('patient_task_queued');
    Queue::assertNothingPushed();
    Http::assertNothingSent();

    $this->artisan('whatsapp:dispatch-patient-tasks')->assertSuccessful();
    Queue::assertPushed(ProcessWhatsAppPatientTaskJob::class, 1);
});

test('a financial request is answered only when its queued task is processed', function () {
    config()->set('whatsapp.patients.enabled', true);
    patientTaskIntegration();
    $clinic = Clinic::factory()->create(['name' => 'Clínica Movimento']);
    enablePatientAutomation($clinic);
    $patient = patientWithWhatsApp($clinic);
    Receivable::query()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'description' => 'Mensalidade de agosto',
        'amount' => 250,
        'amount_received' => 50,
        'due_date' => now()->addDays(5)->toDateString(),
        'status' => 'partially_received',
    ]);
    $task = app(WhatsAppPatientTaskService::class)->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'type' => 'financial_summary',
        'payload' => ['phone' => '5511977777777'],
        'deduplication_key' => 'finance-test-1',
    ], false);
    Http::fake(['https://uazapi.example/send/text' => Http::response(['ok' => true])]);

    app()->call([new ProcessWhatsAppPatientTaskJob($task->id), 'handle']);

    expect($task->refresh()->status)->toBe(WhatsAppPatientTask::STATUS_COMPLETED)
        ->and($task->attempts)->toBe(1);
    Http::assertSent(fn ($request) => $request->hasHeader('token', 'patient-task-token')
        && str_contains($request['text'], 'Clínica Movimento')
        && str_contains($request['text'], 'R$ 200,00'));
});

test('the planner repeats reminders but never duplicates the same attempt', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    $clinic = Clinic::factory()->create();
    enablePatientAutomation($clinic, [
        'reminder_max_attempts' => 3,
        'reminder_repeat_minutes' => 180,
    ]);
    $patient = patientWithWhatsApp($clinic);
    $appointment = patientAppointment($clinic, $patient);

    $this->artisan('whatsapp:plan-patient-reminders')->assertSuccessful();
    $this->artisan('whatsapp:plan-patient-reminders')->assertSuccessful();

    expect(WhatsAppPatientTask::query()->count())->toBe(1);
    $first = WhatsAppPatientTask::query()->sole();
    $first->update([
        'status' => WhatsAppPatientTask::STATUS_COMPLETED,
        'completed_at' => now()->subMinutes(181),
    ]);

    $this->artisan('whatsapp:plan-patient-reminders')->assertSuccessful();
    expect(WhatsAppPatientTask::query()->where('appointment_id', $appointment->id)->count())->toBe(2);
    Queue::assertPushed(ProcessWhatsAppPatientTaskJob::class, 2);
});

test('confirmation from the patient is queued and stops future reminders', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    $secret = 'patient-task-secret-with-at-least-forty-three-characters';
    patientTaskIntegration($secret);
    $clinic = Clinic::factory()->create(['name' => 'Clínica Agenda']);
    enablePatientAutomation($clinic);
    $patient = patientWithWhatsApp($clinic);
    $appointment = patientAppointment($clinic, $patient);
    WhatsAppPatientTask::query()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'appointment_id' => $appointment->id,
        'type' => 'appointment_reminder',
        'status' => WhatsAppPatientTask::STATUS_COMPLETED,
        'payload' => ['reminder_number' => 1],
        'available_at' => now(),
        'completed_at' => now(),
    ]);
    WhatsAppPatientTask::query()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'appointment_id' => $appointment->id,
        'type' => 'appointment_reminder',
        'status' => WhatsAppPatientTask::STATUS_COMPLETED,
        'payload' => ['reminder_number' => 2],
        'available_at' => now(),
        'completed_at' => now()->subMinute(),
    ]);

    $this->postJson(
        route('webhooks.uazapi', [$secret]),
        patientTaskPayload('Confirmar', '5511977777777', 'CONFIRM-1'),
    )->assertAccepted();

    $confirmation = WhatsAppPatientTask::query()->where('type', 'appointment_confirmation')->sole();
    Http::fake(['https://uazapi.example/send/text' => Http::response(['ok' => true])]);
    app()->call([new ProcessWhatsAppPatientTaskJob($confirmation->id), 'handle']);

    expect($appointment->refresh()->status)->toBe('confirmed')
        ->and($confirmation->refresh()->status)->toBe(WhatsAppPatientTask::STATUS_COMPLETED);

    $count = WhatsAppPatientTask::query()->where('type', 'appointment_reminder')->count();
    $this->artisan('whatsapp:plan-patient-reminders')->assertSuccessful();
    expect(WhatsAppPatientTask::query()->where('type', 'appointment_reminder')->count())->toBe($count);
});

test('provider failures leave the task retryable and the sweeper dispatches it again', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    patientTaskIntegration();
    $clinic = Clinic::factory()->create();
    enablePatientAutomation($clinic);
    $patient = patientWithWhatsApp($clinic);
    $task = app(WhatsAppPatientTaskService::class)->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'type' => 'appointment_list',
        'payload' => ['phone' => '5511977777777'],
        'deduplication_key' => 'retry-test-1',
    ], false);
    Http::fake(['https://uazapi.example/send/text' => Http::response(['error' => 'offline'], 503)]);

    expect(fn () => app()->call([new ProcessWhatsAppPatientTaskJob($task->id), 'handle']))
        ->toThrow(RuntimeException::class);

    expect($task->refresh()->status)->toBe(WhatsAppPatientTask::STATUS_RETRYING)
        ->and($task->attempts)->toBe(1);

    $task->update(['available_at' => now()->subSecond()]);
    $this->artisan('whatsapp:dispatch-patient-tasks')->assertSuccessful();
    Queue::assertPushed(ProcessWhatsAppPatientTaskJob::class, fn ($job) => $job->taskId === $task->id);
});

test('the sweeper fails an interrupted task that exhausted its attempts', function () {
    Queue::fake();
    $clinic = Clinic::factory()->create();
    $patient = patientWithWhatsApp($clinic);
    $task = WhatsAppPatientTask::query()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'type' => 'appointment_list',
        'status' => WhatsAppPatientTask::STATUS_PROCESSING,
        'payload' => [],
        'attempts' => 5,
        'max_attempts' => 5,
        'available_at' => now()->subHour(),
        'started_at' => now()->subHour(),
    ]);

    $this->artisan('whatsapp:dispatch-patient-tasks')->assertSuccessful();

    expect($task->refresh()->status)->toBe(WhatsAppPatientTask::STATUS_FAILED)
        ->and($task->failed_at)->not->toBeNull();
    Queue::assertNothingPushed();
});

test('reminders require active consent and patient automation enabled', function () {
    Queue::fake();
    $clinic = Clinic::factory()->create();
    enablePatientAutomation($clinic);
    $patient = patientWithWhatsApp($clinic, consent: false);
    patientAppointment($clinic, $patient);

    config()->set('whatsapp.patients.enabled', true);
    $this->artisan('whatsapp:plan-patient-reminders')->assertSuccessful();
    expect(WhatsAppPatientTask::query()->count())->toBe(0);

    $patient->consents()->create([
        'clinic_id' => $clinic->id,
        'type' => 'whatsapp_messages',
        'document_version' => '1.0',
        'granted_at' => now(),
    ]);
    config()->set('whatsapp.patients.enabled', false);
    $this->artisan('whatsapp:plan-patient-reminders')->assertSuccessful();
    expect(WhatsAppPatientTask::query()->count())->toBe(0);
});

test('a cancellation without a recent reminder waits for the clinic team', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    $secret = 'patient-task-secret-with-at-least-forty-three-characters';
    patientTaskIntegration($secret);
    $clinic = Clinic::factory()->create(['name' => 'Clínica Atendimento']);
    enablePatientAutomation($clinic);
    patientWithWhatsApp($clinic);

    $this->postJson(
        route('webhooks.uazapi', [$secret]),
        patientTaskPayload('Quero cancelar', '5511977777777', 'CANCEL-NO-CONTEXT'),
    )->assertAccepted();

    $task = WhatsAppPatientTask::query()->sole();
    expect($task->type)->toBe('cancellation_request')
        ->and($task->appointment_id)->toBeNull();

    Http::fake(['https://uazapi.example/send/text' => Http::response(['ok' => true])]);
    app()->call([new ProcessWhatsAppPatientTaskJob($task->id), 'handle']);
    expect($task->refresh()->status)->toBe(WhatsAppPatientTask::STATUS_AWAITING_STAFF);
});

test('admin and reception can view only their clinic patient tasks', function () {
    $clinic = Clinic::factory()->create();
    $otherClinic = Clinic::factory()->create();
    $patient = patientWithWhatsApp($clinic);
    $otherPatient = patientWithWhatsApp($otherClinic, '5511966666666');
    $task = WhatsAppPatientTask::query()->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'type' => 'human_support',
        'status' => WhatsAppPatientTask::STATUS_AWAITING_STAFF,
        'payload' => [],
        'available_at' => now(),
    ]);
    WhatsAppPatientTask::query()->create([
        'clinic_id' => $otherClinic->id,
        'patient_id' => $otherPatient->id,
        'type' => 'human_support',
        'status' => WhatsAppPatientTask::STATUS_AWAITING_STAFF,
        'payload' => [],
        'available_at' => now(),
    ]);
    Role::findOrCreate('admin-clinica');
    $admin = User::factory()->create(['clinic_id' => $clinic->id]);
    $admin->assignRole('admin-clinica');
    $this->withoutMiddleware(EnsureClinicHasActiveSubscription::class);

    $this->actingAs($admin)
        ->get(route('whatsapp-patient-tasks.index'))
        ->assertOk()
        ->assertSee($patient->full_name)
        ->assertDontSee($otherPatient->full_name);

    $this->actingAs($admin)
        ->patch(route('whatsapp-patient-tasks.complete', $task))
        ->assertRedirect();
    expect($task->refresh()->status)->toBe(WhatsAppPatientTask::STATUS_COMPLETED);
});

test('existing patient phones can be reindexed after deploy', function () {
    $clinic = Clinic::factory()->create();
    $patient = patientWithWhatsApp($clinic);
    $patient->forceFill(['whatsapp_phone_hash' => null])->saveQuietly();

    $this->artisan('whatsapp:backfill-patient-phones')->assertSuccessful();

    expect($patient->refresh()->whatsapp_phone_hash)->toHaveLength(64);
});

test('patient automation can be enabled and configured for one clinic only', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    $enabledClinic = Clinic::factory()->create(['subdomain' => 'clinic-enabled']);
    $disabledClinic = Clinic::factory()->create(['subdomain' => 'clinic-disabled']);
    $enabledPatient = patientWithWhatsApp($enabledClinic, '5511955555555');
    $disabledPatient = patientWithWhatsApp($disabledClinic, '5511944444444');
    patientAppointment($enabledClinic, $enabledPatient);
    patientAppointment($disabledClinic, $disabledPatient);

    $this->artisan('whatsapp:configure-clinic-patients', [
        'clinic' => 'clinic-enabled',
        '--enable' => true,
        '--hours-before' => 36,
        '--repeat-minutes' => 120,
        '--max-reminders' => 4,
        '--stop-minutes-before' => 45,
    ])->assertSuccessful();
    $this->artisan('whatsapp:plan-patient-reminders')->assertSuccessful();

    $settings = ClinicWhatsAppSetting::query()->sole();
    expect($settings->clinic_id)->toBe($enabledClinic->id)
        ->and($settings->patient_automation_enabled)->toBeTrue()
        ->and($settings->reminder_hours_before)->toBe(36)
        ->and($settings->reminder_repeat_minutes)->toBe(120)
        ->and($settings->reminder_max_attempts)->toBe(4)
        ->and($settings->reminder_stop_minutes_before)->toBe(45)
        ->and(WhatsAppPatientTask::query()->count())->toBe(1)
        ->and(WhatsAppPatientTask::query()->sole()->clinic_id)->toBe($enabledClinic->id);
});

test('a patient registered only in disabled clinics is not routed to automation', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    $secret = 'patient-task-secret-with-at-least-forty-three-characters';
    patientTaskIntegration($secret);
    $clinic = Clinic::factory()->create();
    patientWithWhatsApp($clinic);

    $this->postJson(
        route('webhooks.uazapi', [$secret]),
        patientTaskPayload('Financeiro', '5511977777777', 'DISABLED-CLINIC-1'),
    )->assertAccepted();

    expect(WhatsAppPatientTask::query()->count())->toBe(0);
    Queue::assertNothingPushed();
});

test('a patient without active consent is not routed to automation', function () {
    Queue::fake();
    config()->set('whatsapp.patients.enabled', true);
    $secret = 'patient-task-secret-with-at-least-forty-three-characters';
    patientTaskIntegration($secret);
    $clinic = Clinic::factory()->create();
    enablePatientAutomation($clinic);
    patientWithWhatsApp($clinic, consent: false);

    $this->postJson(
        route('webhooks.uazapi', [$secret]),
        patientTaskPayload('Financeiro', '5511977777777', 'NO-CONSENT-1'),
    )->assertAccepted();

    expect(WhatsAppPatientTask::query()->count())->toBe(0);
    Queue::assertNothingPushed();
});

test('consent is checked again before a queued patient response is sent', function () {
    config()->set('whatsapp.patients.enabled', true);
    patientTaskIntegration();
    $clinic = Clinic::factory()->create();
    enablePatientAutomation($clinic);
    $patient = patientWithWhatsApp($clinic);
    $task = app(WhatsAppPatientTaskService::class)->create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'type' => 'financial_summary',
        'payload' => ['phone' => '5511977777777'],
        'deduplication_key' => 'revoked-consent-test-1',
    ], false);
    $patient->consents()->update(['revoked_at' => now()]);
    Http::fake();

    app()->call([new ProcessWhatsAppPatientTaskJob($task->id), 'handle']);

    expect($task->refresh()->status)->toBe(WhatsAppPatientTask::STATUS_CANCELED)
        ->and($task->result['reason'])->toContain('sem consentimento');
    Http::assertNothingSent();
});
