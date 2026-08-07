<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppMessageLog extends Model
{
    protected $table = 'whatsapp_message_logs';

    protected $fillable = [
        'clinic_id',
        'provider',
        'phone',
        'message',
        'payload',
        'response',
        'status',
        'error',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
    ];
}
