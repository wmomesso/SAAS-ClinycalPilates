<?php

namespace App\Models\Clinics\Clinic\Appointment;

use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'professional_id',
        'room_id',
        'service_type_id',
        'start_time',
        'end_time',
        'status',
        'notes',
        'recurrence_rule',
        'parent_appointment_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
