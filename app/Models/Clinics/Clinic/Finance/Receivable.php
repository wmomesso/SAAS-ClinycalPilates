<?php

namespace App\Models\Clinics\Clinic\Finance;

use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\User;
use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Receivable extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'bank_account_id',
        'patient_id',
        'invoice_id',
        'patient_package_id',
        'description',
        'amount',
        'payment_method',
        'payment_method_id',
        'payment_source_type',
        'payment_source_id',
        'due_date',
        'receipt_date',
        'amount_received',
        'status',
        'notes',
        'reconciled_date',
        'reconciled_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'due_date' => 'date',
        'receipt_date' => 'date',
        'reconciled_date' => 'date',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patientPackage(): BelongsTo
    {
        return $this->belongsTo(PatientPackage::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function paymentSource(): MorphTo
    {
        return $this->morphTo();
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
