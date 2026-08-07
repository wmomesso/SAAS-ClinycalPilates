<?php

namespace App\Models;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SecurityAuditLog extends Model
{
    use BelongsToClinic;

    public const UPDATED_AT = null;

    protected $fillable = [
        'clinic_id',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'encrypted:array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function record(string $event, Model $auditable, array $metadata = []): void
    {
        $request = app()->bound('request') ? request() : null;

        static::withoutEvents(fn () => static::create([
            'clinic_id' => static::clinicIdFor($auditable),
            'user_id' => auth()->id(),
            'event' => $event,
            'auditable_type' => $auditable->getMorphClass(),
            'auditable_id' => $auditable->getKey(),
            'ip_address' => $request?->ip(),
            'user_agent' => mb_substr((string) $request?->userAgent(), 0, 500),
            'metadata' => $metadata,
        ]));
    }

    public static function clinicIdFor(Model $model): ?int
    {
        $clinicId = $model->getAttribute('clinic_id');
        if ($clinicId) {
            return (int) $clinicId;
        }

        if ($model instanceof User) {
            return $model->clinic_id ? (int) $model->clinic_id : null;
        }

        if (method_exists($model, 'patient')) {
            return $model->patient()->withoutGlobalScopes()->value('clinic_id');
        }

        if (method_exists($model, 'invoice')) {
            return $model->invoice()->withoutGlobalScopes()->value('clinic_id');
        }

        if (method_exists($model, 'stockItem')) {
            return $model->stockItem()->withoutGlobalScopes()->value('clinic_id');
        }

        return auth()->user()?->clinic_id;
    }
}
