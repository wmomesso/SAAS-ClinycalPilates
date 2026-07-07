<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('super admins are redirected to the admin dashboard after login', function () {
    Role::create(['name' => 'super-admin']);
    $user = User::factory()->create([
        'email' => 'wagner.momesso@criacoder.com.br',
    ]);
    $user->assignRole('super-admin');

    $response = $this->post('/login', [
        'email' => 'wagner.momesso@criacoder.com.br',
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('super admins visiting the clinic dashboard are redirected to the admin dashboard', function () {
    Role::create(['name' => 'super-admin']);
    $user = User::factory()->create()->assignRole('super-admin');

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
