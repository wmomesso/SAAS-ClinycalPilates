<?php

namespace App\Models\Clinics\Clinic\HealthInsurance;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceGuide extends Model
{
    use BelongsToClinic, HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'health_insurance_id',
        'guide_type',
        'auth_code',
        'status',
        'total_value',
        'paid_value',
        'total_sessions',
        'used_sessions',
        'issue_date',
        'expiration_date',
        'billing_date',
        'payment_expected_date',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_value' => 'decimal:2',
            'paid_value' => 'decimal:2',
            'total_sessions' => 'integer',
            'used_sessions' => 'integer',
            'issue_date' => 'date',
            'expiration_date' => 'date',
            'billing_date' => 'date',
            'payment_expected_date' => 'date',
            'payment_date' => 'date',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function healthInsurance(): BelongsTo
    {
        return $this->belongsTo(HealthInsurance::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
