<?php

namespace App\Providers;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Finance\BankAccount;
use App\Models\Clinics\Clinic\Finance\Invoice;
use App\Models\Clinics\Clinic\Finance\PatientPackage;
use App\Models\Clinics\Clinic\Finance\Payable;
use App\Models\Clinics\Clinic\Finance\Receivable;
use App\Models\Clinics\Clinic\Finance\ServicePackage;
use App\Models\Clinics\Clinic\HealthInsurance\HealthInsurance;
use App\Models\Clinics\Clinic\HealthInsurance\InsuranceGuide;
use App\Models\Clinics\Clinic\Patient\Anamnesis;
use App\Models\Clinics\Clinic\Patient\Evolution;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Patient\PatientConsent;
use App\Models\Clinics\Clinic\Patient\PatientDocument;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\Clinics\Clinic\WareHouse\EquipmentMaintenanceLog;
use App\Models\Clinics\Clinic\WareHouse\StockItem;
use App\Models\Clinics\Clinic\WareHouse\StockMovement;
use App\Models\SAAS\SubscriptionPlan;
use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Observers\PatientWhatsAppPhoneObserver;
use App\Observers\SecurityAuditObserver;
use App\Policies\Clinics\Clinic\Appointment\AppointmentPolicy;
use App\Policies\Clinics\Clinic\ClinicPolicy;
use App\Policies\Clinics\Clinic\Finance\BankAccountPolicy;
use App\Policies\Clinics\Clinic\Finance\InvoicePolicy;
use App\Policies\Clinics\Clinic\Finance\PayablePolicy;
use App\Policies\Clinics\Clinic\Finance\ReceivablePolicy;
use App\Policies\Clinics\Clinic\Finance\ServicePackagePolicy;
use App\Policies\Clinics\Clinic\HealthInsurance\HealthInsurancePolicy;
use App\Policies\Clinics\Clinic\HealthInsurance\InsuranceGuidePolicy;
use App\Policies\Clinics\Clinic\PatientPolicy;
use App\Policies\Clinics\Clinic\Room\RoomPolicy;
use App\Policies\Clinics\Clinic\Services\ServiceTypePolicy;
use App\Policies\Clinics\Clinic\WareHouse\StockItemPolicy;
use App\Policies\SAAS\PlanPolicy;
use App\Policies\SecurityAuditLogPolicy;
use App\Policies\UserPolicy;
use App\Services\Transcription\Contracts\AudioTranscriberInterface;
use App\Services\Transcription\WhisperCppTranscriber;
use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use App\Services\WhatsApp\Providers\UazapiProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AudioTranscriberInterface::class, WhisperCppTranscriber::class);

        $this->app->bind(WhatsAppProviderInterface::class, function () {
            return match (strtolower((string) config('whatsapp.provider', 'uazapi'))) {
                'uazapi' => new UazapiProvider,
                // TODO: Adicionar aqui o provider oficial da Meta quando a integracao for implementada.
                default => throw new InvalidArgumentException('Unsupported WhatsApp provider configured.'),
            };
        });
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
        Gate::policy(BankAccount::class, BankAccountPolicy::class);
        Gate::policy(Clinic::class, ClinicPolicy::class);
        Gate::policy(HealthInsurance::class, HealthInsurancePolicy::class);
        Gate::policy(InsuranceGuide::class, InsuranceGuidePolicy::class);
        Gate::policy(Invoice::class, InvoicePolicy::class);
        Gate::policy(Payable::class, PayablePolicy::class);
        Gate::policy(Receivable::class, ReceivablePolicy::class);
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(SubscriptionPlan::class, PlanPolicy::class);
        Gate::policy(ServicePackage::class, ServicePackagePolicy::class);
        Gate::policy(ServiceType::class, ServiceTypePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(StockItem::class, StockItemPolicy::class);
        Gate::policy(SecurityAuditLog::class, SecurityAuditLogPolicy::class);

        Patient::observe(PatientWhatsAppPhoneObserver::class);

        foreach ([
            User::class,
            Patient::class,
            Anamnesis::class,
            Evolution::class,
            PatientDocument::class,
            PatientConsent::class,
            Appointment::class,
            PatientPackage::class,
            Invoice::class,
            Payable::class,
            Receivable::class,
            BankAccount::class,
            HealthInsurance::class,
            InsuranceGuide::class,
            StockItem::class,
            StockMovement::class,
            EquipmentMaintenanceLog::class,
        ] as $auditedModel) {
            $auditedModel::observe(SecurityAuditObserver::class);
        }
    }
}
