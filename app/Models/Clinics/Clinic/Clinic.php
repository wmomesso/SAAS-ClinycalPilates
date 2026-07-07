<?php

namespace App\Models\Clinics\Clinic;

use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\Clinics\Clinic\Room\Room;
use App\Models\SAAS\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

/**
 * Modelo para Clinic
 */
class Clinic extends Model
{
    use HasFactory, Billable;

    protected $fillable = [
        'name',
        'subdomain',
        'document',
        'logo_path',
        'owner_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
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
