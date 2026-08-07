<?php

namespace App\Models;

use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicWhatsAppSetting extends Model
{
    protected $table = 'clinic_whatsapp_settings';

    protected $fillable = [
        'clinic_id',
        'patient_automation_enabled',
        'reminder_hours_before',
        'reminder_repeat_minutes',
        'reminder_max_attempts',
        'reminder_stop_minutes_before',
    ];

    protected function casts(): array
    {
        return [
            'patient_automation_enabled' => 'boolean',
            'reminder_hours_before' => 'integer',
            'reminder_repeat_minutes' => 'integer',
            'reminder_max_attempts' => 'integer',
            'reminder_stop_minutes_before' => 'integer',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
