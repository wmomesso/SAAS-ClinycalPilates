<?php

namespace App\Models\Clinics\Clinic\Patient;

use App\Models\Clinics\Clinic\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para Evolution
 */
class Evolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'professional_id',
        'description',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
