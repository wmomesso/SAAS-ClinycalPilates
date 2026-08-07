<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin-clinica') && $user->clinic_id !== null;
    }

    public function view(User $user, User $target): bool
    {
        return $this->sameClinicAdmin($user, $target);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin-clinica') && $user->clinic_id !== null;
    }

    public function update(User $user, User $target): bool
    {
        return $this->sameClinicAdmin($user, $target) && ! $target->hasRole('super-admin');
    }

    public function delete(User $user, User $target): bool
    {
        return $this->sameClinicAdmin($user, $target)
            && $user->id !== $target->id
            && $target->clinic?->owner_id !== $target->id
            && ! $target->hasRole('super-admin');
    }

    private function sameClinicAdmin(User $user, User $target): bool
    {
        return $user->hasRole('admin-clinica')
            && $user->clinic_id !== null
            && $user->clinic_id === $target->clinic_id;
    }
}
