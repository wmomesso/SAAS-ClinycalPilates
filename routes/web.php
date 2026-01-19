<?php

use App\Http\Controllers\Clinics\Clinic\Appointments\AppointmentController;
use App\Http\Controllers\Clinics\Clinic\ClinicController;
use App\Http\Controllers\Clinics\Clinic\Finance\InvoiceController;
use App\Http\Controllers\Clinics\Clinic\Finance\ServicePackageController;
use App\Http\Controllers\Clinics\Clinic\Patients\PatientController;
use App\Http\Controllers\Clinics\Clinic\Rooms\RoomController;
use App\Http\Controllers\Clinics\Clinic\Services\ServiceTypeController;
use App\Http\Controllers\Clinics\Clinic\Subscriptions\SubscriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SAAS\PlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clinics\RolesPermissions\ClinicUserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('saas.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestão da Clínica (Configurações e Usuários)
    Route::resource('clinic-users', ClinicUserController::class);
    Route::get('clinic-settings', [ClinicController::class, 'settings'])->name('clinic.settings');
    Route::put('clinic-settings', [ClinicController::class, 'update'])->name('clinic.update');

    // Sprint 2: Gestão de Pacientes
    // O resource cria automaticamente rotas para index, create, store, show, edit, update e destroy
    Route::resource('patients', PatientController::class);

    // --- Sprint 3: Gestão de Salas ---
    // Namespace: App\Http\Controllers\Clinics\Clinic\Rooms
    Route::resource('rooms', RoomController::class);

    // --- Sprint 4: Gestão de Agendamentos e Serviços ---
    Route::resource('service-types', ServiceTypeController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

    // --- Sprint 5: Gestão Financeira (Faturamento e Pacotes) ---
    Route::resource('service-packages', ServicePackageController::class);
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'addPayment'])->name('invoices.payment');

    // --- Sprint 6: Módulo SAAS (Assinaturas e Planos) ---
    // Rotas para a Clínica gerenciar sua assinatura
    Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('subscription/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('subscription.billing');

    // Rotas de Administração Global (Super Admin)
    Route::resource('plans', PlanController::class);
    Route::get('clinics', [ClinicController::class, 'index'])->name('clinics.index');
});

require __DIR__.'/auth.php';
