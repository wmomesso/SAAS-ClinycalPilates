<?php

namespace App\Models\Clinics\Clinic\Finance;

use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPackage extends Model
{
    protected $table = 'patient_packages';

    use HasFactory;

    protected $fillable = [
        'patient_id',
        'service_package_id',
        'invoice_id',
        'bank_account_id',
        'total_sessions',
        'used_sessions',
        'missed_sessions',
        'start_date',
        'end_date',
        'status',
        'price_paid',
        'billing_type',
        'payment_method',
        'billing_day',
        'next_billing_date',
        'canceled_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'canceled_at' => 'datetime',
    ];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function package(): BelongsTo { return $this->belongsTo(ServicePackage::class, 'service_package_id'); }
    public function bankAccount(): BelongsTo { return $this->belongsTo(BankAccount::class); }

    public function getConsumedSessionsAttribute(): int
    {
        return (int) $this->used_sessions + (int) $this->missed_sessions;
    }

    public function getRemainingSessionsAttribute(): int
    {
        return max(0, (int) $this->total_sessions - $this->consumed_sessions);
    }
}
