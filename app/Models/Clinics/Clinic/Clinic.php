<?php

namespace App\Models\Clinics\Clinic;

use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\ClinicWhatsAppSetting;
use App\Models\SAAS\SubscriptionPlan;
use App\Models\User;
use App\Models\WhatsAppPhoneBinding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;

/**
 * Modelo para Clinic
 */
class Clinic extends Model
{
    use Billable, HasFactory;

    public const DEFAULT_TRIAL_DAYS = 7;

    protected $fillable = [
        'name',
        'subdomain',
        'document',
        'logo_path',
        'owner_id',
        'trial_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    public function hasActiveSubscriptionOrTrial(): bool
    {
        return $this->subscribed('default') || $this->onGenericTrial();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function whatsAppPhoneBindings(): HasMany
    {
        return $this->hasMany(WhatsAppPhoneBinding::class);
    }

    public function whatsAppSettings(): HasOne
    {
        return $this->hasOne(ClinicWhatsAppSetting::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function currentSubscriptionPlan(): ?SubscriptionPlan
    {
        $subscription = $this->subscription('default');

        if (! $subscription || ! $subscription->valid()) {
            return null;
        }

        $stripePrice = $subscription->stripe_price
            ?? $subscription->items()->first()?->stripe_price;

        if (! $stripePrice) {
            return null;
        }

        return SubscriptionPlan::where('stripe_plan_id', $stripePrice)->first();
    }

    public function subscriptionLimit(string $limitColumn): ?int
    {
        $limit = $this->currentSubscriptionPlan()?->{$limitColumn};

        return $limit === null ? null : (int) $limit;
    }

    public function hasReachedSubscriptionLimit(string $limitColumn, int $currentCount): bool
    {
        $limit = $this->subscriptionLimit($limitColumn);

        return $limit !== null && $currentCount >= $limit;
    }
}
