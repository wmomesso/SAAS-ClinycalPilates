<?php

namespace App\Policies\SAAS;

use App\Models\SAAS\SubscriptionPlan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanPolicy
{
    /**
     * Apenas o Super Admin tem permissão para gerir os planos SAAS.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, SubscriptionPlan $plan): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, SubscriptionPlan $plan): bool
    {
        return false;
    }

    public function delete(User $user, SubscriptionPlan $plan): bool
    {
        return false;
    }
}
