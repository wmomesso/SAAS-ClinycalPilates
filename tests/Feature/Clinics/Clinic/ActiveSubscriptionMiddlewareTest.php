<?php

use App\Models\Clinics\Clinic\Clinic;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
    Permission::firstOrCreate(['name' => 'gerenciar-assinatura-saas']);
});

function clinicUserForSubscriptionMiddleware(array $attributes = []): array
{
    $clinic = Clinic::factory()->create();
    $user = User::factory()->create([
        'clinic_id' => $clinic->id,
        ...$attributes,
    ]);

    return [$clinic, $user];
}

test('users who can manage subscription are redirected to plans when clinic has no active plan', function () {
    [, $user] = clinicUserForSubscriptionMiddleware();
    $user->givePermissionTo('gerenciar-assinatura-saas');

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('subscription.index'));
    $response->assertSessionHas('warning', 'Sua clínica não possui um plano ativo. Escolha um plano para continuar.');
});

test('users who cannot manage subscription are told to contact the clinic manager', function () {
    [, $user] = clinicUserForSubscriptionMiddleware();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('subscription.index'));
    $response->assertSessionHas('warning', 'O plano da clínica está expirado. Informe o gestor da clínica para regularizar a assinatura.');
});

test('users can access clinic administration when clinic has an active plan', function () {
    [$clinic, $user] = clinicUserForSubscriptionMiddleware();

    $clinic->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_active',
        'stripe_status' => 'active',
        'stripe_price' => 'price_test_active',
        'quantity' => 1,
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

test('users can access clinic administration during the free trial', function () {
    [$clinic, $user] = clinicUserForSubscriptionMiddleware();
    $clinic->update([
        'trial_ends_at' => now()->addDays(7),
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});

test('users are redirected when the free trial has expired and no plan is active', function () {
    [$clinic, $user] = clinicUserForSubscriptionMiddleware();
    $clinic->update([
        'trial_ends_at' => now()->subDay(),
    ]);
    $user->givePermissionTo('gerenciar-assinatura-saas');

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('subscription.index'));
    $response->assertSessionHas('warning', 'Sua clínica não possui um plano ativo. Escolha um plano para continuar.');
});

test('users without subscription management permission cannot start checkout', function () {
    [, $user] = clinicUserForSubscriptionMiddleware();

    $response = $this->actingAs($user)->post(route('subscription.checkout'), [
        'plan_id' => 'price_test',
    ]);

    $response->assertRedirect(route('subscription.index'));
    $response->assertSessionHas('warning', 'Você não tem permissão para contratar planos. Informe o gestor da clínica que o plano está expirado.');
});
