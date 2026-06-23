<?php

namespace App\Policies\Clinics\Clinic\Services;

use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceTypePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ServiceType $serviceType): bool
    {
        return $user->clinic_id === $serviceType->clinic_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ServiceType $serviceType): bool
    {
        return $user->clinic_id === $serviceType->clinic_id;
    }

    public function delete(User $user, ServiceType $serviceType): bool
    {
        return $user->clinic_id === $serviceType->clinic_id;
    }
}
