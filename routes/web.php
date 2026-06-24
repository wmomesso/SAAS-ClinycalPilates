<?php

use App\Http\Controllers\Clinics\Clinic\Appointments\AppointmentController;
use App\Http\Controllers\Clinics\Clinic\ClinicController;
use App\Http\Controllers\Clinics\Clinic\Finance\InvoiceController;
use App\Http\Controllers\Clinics\Clinic\Finance\ServicePackageController;
use App\Http\Controllers\Clinics\Clinic\HealthInsurance\HealthInsuranceController;
use App\Http\Controllers\Clinics\Clinic\HealthInsurance\InsuranceGuideController;
use App\Http\Controllers\Clinics\Clinic\Patients\PatientController;
use App\Http\Controllers\Clinics\Clinic\RolePermissionController;
use App\Http\Controllers\Clinics\Clinic\Rooms\RoomController;
use App\Http\Controllers\Clinics\Clinic\Services\ServiceTypeController;
use App\Http\Controllers\Clinics\Clinic\Subscriptions\SubscriptionController;
use App\Http\Controllers\Clinics\Finance\BankAccountController;
use App\Http\Controllers\Clinics\Finance\PayableController;
use App\Http\Controllers\Clinics\Finance\ReceivableController;
use App\Http\Controllers\Clinics\RolesPermissions\ClinicUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SAAS\PlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', \App\Http\Controllers\Clinics\Clinic\DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestão da Clínica (Configurações e Usuários)
    Route::resource('clinic-users', ClinicUserController::class);
    Route::get('clinic-settings', [ClinicController::class, 'settings'])->name('clinic.settings');
    Route::put('clinic-settings', [ClinicController::class, 'update'])->name('clinic.update');
    Route::get('roles/{role}/permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
    Route::post('roles/{role}/permissions', [RolePermissionController::class, 'update'])->name('roles.permissions.update');

    // Sprint 2: Gestão de Pacientes
    // O resource cria automaticamente rotas para index, create, store, show, edit, update e destroy
    Route::get('patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::resource('patients', PatientController::class);
    Route::prefix('patients/{patient}')->name('patients.')->group(function () {
        Route::post('evolutions', [\App\Http\Controllers\Clinics\Clinic\Patients\EvolutionController::class, 'store'])->name('evolutions.store');
        Route::post('documents', [\App\Http\Controllers\Clinics\Clinic\Patients\PatientDocumentController::class, 'store'])->name('documents.store');
        Route::post('anamneses', [\App\Http\Controllers\Clinics\Clinic\Patients\AnamnesisController::class, 'store'])->name('anamneses.store');
        Route::get('anamneses/compare', [\App\Http\Controllers\Clinics\Clinic\Patients\AnamnesisController::class, 'compare'])->name('anamneses.compare');
    });
    Route::delete('evolutions/{evolution}', [\App\Http\Controllers\Clinics\Clinic\Patients\EvolutionController::class, 'destroy'])->name('evolutions.destroy');
    Route::delete('documents/{document}', [\App\Http\Controllers\Clinics\Clinic\Patients\PatientDocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('anamneses/{anamnesis}', [\App\Http\Controllers\Clinics\Clinic\Patients\AnamnesisController::class, 'show'])->name('anamneses.show');

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

    // --- Gestão Financeira (Contas e Fluxo) ---
    Route::resource('bank-accounts', BankAccountController::class);
    Route::resource('payables', PayableController::class);
    Route::resource('receivables', ReceivableController::class);

    // --- Sprint 7: Módulo de Convênios ---
    Route::resource('health-insurances', HealthInsuranceController::class);
    Route::resource('insurance-guides', InsuranceGuideController::class);

    // --- Sprint 6: Módulo SAAS (Assinaturas e Planos) ---
    // Rotas para a Clínica gerenciar sua assinatura
    Route::get('subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('subscription/checkout', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('subscription/billing-portal', [SubscriptionController::class, 'billingPortal'])->name('subscription.billing');

    // Rotas de Administração Global (Super Admin)
    Route::middleware(['role:super-admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', \App\Http\Controllers\SAAS\AdminDashboardController::class)->name('admin.dashboard');
        Route::resource('plans', PlanController::class);
        Route::get('clinics', [ClinicController::class, 'index'])->name('admin.clinics.index');
    });
});

require __DIR__.'/auth.php';
