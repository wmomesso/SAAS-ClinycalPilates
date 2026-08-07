<?php

namespace App\Models;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\Clinics\Clinic\Clinic;
use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class WhatsAppPatientTask extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_RETRYING = 'retrying';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_AWAITING_STAFF = 'awaiting_staff';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELED = 'canceled';

    protected $table = 'whatsapp_patient_tasks';

    protected $fillable = [
        'uuid',
        'clinic_id',
        'patient_id',
        'appointment_id',
        'webhook_event_id',
        'type',
        'status',
        'priority',
        'deduplication_key',
        'payload',
        'result',
        'attempts',
        'max_attempts',
        'available_at',
        'started_at',
        'completed_at',
        'failed_at',
        'last_error',
    ];

    protected $hidden = [
        'payload',
        'result',
        'deduplication_key',
        'last_error',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $task): void {
            $task->uuid ??= (string) Str::uuid();
        });
    }

    protected function casts(): array
    {
        return [
            'payload' => 'encrypted:array',
            'result' => 'encrypted:array',
            'last_error' => 'encrypted',
            'available_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function webhookEvent(): BelongsTo
    {
        return $this->belongsTo(WhatsAppWebhookEvent::class, 'webhook_event_id');
    }
}
