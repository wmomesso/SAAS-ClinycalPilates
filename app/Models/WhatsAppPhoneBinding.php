<?php

namespace App\Models;

use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppPhoneBinding extends Model
{
    protected $table = 'whatsapp_phone_bindings';

    protected $fillable = [
        'user_id',
        'clinic_id',
        'phone',
        'phone_hash',
        'is_active',
        'bound_at',
    ];

    protected $hidden = ['phone', 'phone_hash'];

    protected function casts(): array
    {
        return [
            'phone' => 'encrypted',
            'is_active' => 'boolean',
            'bound_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
