<?php

namespace App\Models\Clinics\Clinic\Finance;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'name',
        'bank_name',
        'agency',
        'account_number',
        'initial_balance',
        'has_pix',
        'pix_key_type',
        'pix_key',
        'issues_bank_slips',
        'is_active',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'has_pix' => 'boolean',
        'issues_bank_slips' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class);
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }

    public function patientPackages(): HasMany
    {
        return $this->hasMany(PatientPackage::class);
    }
}
