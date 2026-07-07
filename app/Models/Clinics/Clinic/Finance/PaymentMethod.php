<?php

namespace App\Models\Clinics\Clinic\Finance;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id',
        'name',
        'type',
        'requires_bank_account',
        'is_active',
    ];

    protected $casts = [
        'requires_bank_account' => 'boolean',
        'is_active' => 'boolean',
    ];
}
