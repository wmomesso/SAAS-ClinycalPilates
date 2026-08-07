<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class WhatsAppIntegration extends Model
{
    protected $table = 'whatsapp_integrations';

    protected $fillable = [
        'uuid',
        'provider',
        'base_url',
        'instance_id',
        'public_number',
        'token',
        'webhook_secret_hash',
        'is_active',
        'webhook_registered_at',
    ];

    protected $hidden = [
        'token',
        'webhook_secret_hash',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $integration): void {
            $integration->uuid ??= (string) Str::uuid();
        });
    }

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
            'is_active' => 'boolean',
            'webhook_registered_at' => 'datetime',
        ];
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(WhatsAppWebhookEvent::class);
    }
}
