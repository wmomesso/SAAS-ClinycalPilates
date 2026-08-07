<?php

use App\Notifications\Auth\CompleteRegistration;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('users can request a registration link with only their email', function () {
    Notification::fake();

    $response = $this->post('/register', [
        'email' => 'test@example.com',
    ]);

    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
    $response->assertSessionHas('status', 'Enviamos um link de cadastro para o seu e-mail. Acesse o link recebido para criar sua senha e iniciar o teste grátis.');

    Notification::assertSentOnDemand(CompleteRegistration::class);
});

test('new users can complete registration from signed email link', function () {
    $this->travelTo(now());

    $registrationUrl = URL::temporarySignedRoute(
        'register.complete',
        now()->addMinutes(60),
        ['email' => 'test@example.com'],
    );

    $this->get($registrationUrl)
        ->assertOk()
        ->assertSee('test@example.com');

    $response = $this->post($registrationUrl, [
        'clinic_name' => 'Test Clinic',
        'name' => 'Test User',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $clinic = auth()->user()->clinic;

    expect($clinic->trial_ends_at->isSameSecond(now()->addDays(7)))->toBeTrue();
});

test('users cannot complete registration without a valid signed link', function () {
    $response = $this->post('/register/complete?email=test@example.com', [
        'clinic_name' => 'Test Clinic',
        'name' => 'Test User',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertForbidden();
    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
});
