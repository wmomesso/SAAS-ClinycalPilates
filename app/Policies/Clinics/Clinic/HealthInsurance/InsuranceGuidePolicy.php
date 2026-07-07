<?php

namespace App\Policies\Clinics\Clinic\HealthInsurance;

use App\Models\Clinics\Clinic\HealthInsurance\InsuranceGuide;
use App\Models\User;

class InsuranceGuidePolicy
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
        return $user->hasRole('admin-clinica') || $user->can('visualizar-convenios');
    }

    public function view(User $user, InsuranceGuide $insuranceGuide): bool
    {
        return $user->clinic_id === $insuranceGuide->clinic_id && $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin-clinica') || $user->can('gerenciar-convenios');
    }

    public function update(User $user, InsuranceGuide $insuranceGuide): bool
    {
        return $user->clinic_id === $insuranceGuide->clinic_id && $this->create($user);
    }

    public function delete(User $user, InsuranceGuide $insuranceGuide): bool
    {
        return $user->clinic_id === $insuranceGuide->clinic_id && $this->create($user);
    }
}
