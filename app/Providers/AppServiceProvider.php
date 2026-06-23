<?php

namespace App\Providers;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Finance\Invoice;
use App\Models\Clinics\Clinic\Finance\ServicePackage;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\SAAS\SubscriptionPlan;
use App\Policies\Clinics\Clinic\Appointment\AppointmentPolicy;
use App\Policies\Clinics\Clinic\ClinicPolicy;
use App\Policies\Clinics\Clinic\Finance\InvoicePolicy;
use App\Policies\Clinics\Clinic\Finance\ServicePackagePolicy;
use App\Policies\Clinics\Clinic\PatientPolicy;
use App\Policies\Clinics\Clinic\Room\RoomPolicy;
use App\Policies\Clinics\Clinic\Services\ServiceTypePolicy;
use App\Policies\SAAS\PlanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(Clinic::class, ClinicPolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(SubscriptionPlan::class, PlanPolicy::class);
        Gate::policy(ServicePackage::class, ServicePackagePolicy::class);
        Gate::policy(ServiceType::class, ServiceTypePolicy::class);
    }
}
