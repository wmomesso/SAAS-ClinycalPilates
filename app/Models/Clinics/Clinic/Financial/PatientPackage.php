<?php

namespace App\Models\Clinics\Clinic\Financial;

use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPackage extends Model
{
    protected $table = 'patient_packages';

    use HasFactory;
    protected $fillable = ['patient_id', 'package_id', 'invoice_id', 'total_sessions', 'used_sessions', 'start_date', 'end_date', 'status', 'price_paid'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function package(): BelongsTo { return $this->belongsTo(ServicePackage::class); }
}
