<?php

namespace App\Models\Clinics\Clinic\Finance;

use App\Models\Traits\BelongsToClinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payable extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'bank_account_id',
        'description',
        'provider',
        'amount',
        'payment_method_id',
        'due_date',
        'payment_date',
        'amount_paid',
        'status',
        'notes',
        'reconciled_date',
        'reconciled_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'reconciled_date' => 'date',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
