<?php

namespace App\Models\Clinics\Clinic\Patient;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Finance\PatientPackage;
use App\Models\Clinics\Clinic\Finance\Receivable;
use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use BelongsToClinic, HasFactory, SoftDeletes;

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
        'medications',
        'allergies',
        'lifestyle_habits',
        'blood_type',
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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderBy('start_time', 'desc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatientDocument::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(PatientPackage::class)->orderBy('start_date', 'desc');
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class)->orderBy('due_date', 'desc');
    }
}
