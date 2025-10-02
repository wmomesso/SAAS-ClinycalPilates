<?php

use App\Http\Controllers\Clinics\Clinic\ClinicController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Clinics\RolesPermissions\ClinicUserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('saas.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clinic-users', ClinicUserController::class);
    Route::get('clinic-settings', [ClinicController::class, 'settings'])->name('clinic.settings');
    Route::put('clinic-settings', [ClinicController::class, 'update'])->name('clinic.update');
    Route::get('clinics', [ClinicController::class, 'index'])->name('clinics.index');
});

require __DIR__.'/auth.php';
