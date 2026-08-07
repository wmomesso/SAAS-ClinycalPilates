<?php

namespace App\Models;

use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppWebhookEvent extends Model
{
    protected $table = 'whatsapp_webhook_events';

    protected $fillable = [
        'clinic_id',
        'user_id',
        'patient_id',
        'whatsapp_integration_id',
        'provider_event_id',
        'event',
        'message_type',
        'sender_hash',
        'payload',
        'status',
        'media_path',
        'media_mime',
        'transcription',
        'error',
        'received_at',
        'processed_at',
    ];

    protected $hidden = [
        'payload',
        'sender_hash',
        'transcription',
        'error',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'encrypted:array',
            'transcription' => 'encrypted',
            'error' => 'encrypted',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Clinics\Clinic\Patient\Patient::class);
    }

    public function integration(): BelongsTo
    {
        return $this->belongsTo(WhatsAppIntegration::class, 'whatsapp_integration_id');
    }
}
