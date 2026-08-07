<?php

namespace App\Policies\Clinics\Clinic\Finance;

use App\Models\Clinics\Clinic\Finance\ServicePackage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePackagePolicy
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
        return $user->hasRole('admin-clinica')
            || $user->can('visualizar-financeiro')
            || $user->can('gerenciar-financeiro');
    }

    public function view(User $user, ServicePackage $servicePackage): bool
    {
        return $user->clinic_id === $servicePackage->clinic_id && $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin-clinica') || $user->can('gerenciar-financeiro');
    }

    public function update(User $user, ServicePackage $servicePackage): bool
    {
        return $user->clinic_id === $servicePackage->clinic_id && $this->create($user);
    }

    public function delete(User $user, ServicePackage $servicePackage): bool
    {
        return $user->clinic_id === $servicePackage->clinic_id && $this->create($user);
    }
}
