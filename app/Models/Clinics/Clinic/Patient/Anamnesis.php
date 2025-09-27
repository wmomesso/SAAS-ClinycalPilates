<?php

namespace App\Models\Clinics\Clinic\Patient;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para Anamnesis
 */
class Anamnesis extends Model
{
    use HasFactory;

    protected $table = 'anamneses';

    protected $fillable = [
        'patient_id',
        'professional_id',
        'main_complaint',
        'history_of_current_illness',
        'dynamic_form',
    ];

    protected $casts = [
        'dynamic_form' => 'array',
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
