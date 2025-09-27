<?php

namespace App\Models\Clinics\Clinic\Patient;

use App\Models\Clinics\Clinic\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para PatientDocument
 */
class PatientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'uploaded_by_id',
        'name',
        'file_path',
        'mime_type',
        'size',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}
