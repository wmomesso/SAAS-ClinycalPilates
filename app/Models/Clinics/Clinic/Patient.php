<?php

namespace App\Models\Clinics\Clinic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'full_name',
        'birth_date',
        'document_cpf',
        'email',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'medical_history',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'address' => 'array',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function anamneses(): HasMany
    {
        return $this->hasMany(Anamnesis::class)->orderBy('created_at', 'desc');
    }

    public function evolutions(): HasMany
    {
        return $this->hasMany(Evolution::class)->orderBy('created_at', 'desc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatientDocument::class);
    }
}

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
