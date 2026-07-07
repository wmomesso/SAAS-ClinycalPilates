<?php

namespace App\Policies\Clinics\Clinic\Finance;

use App\Models\Clinics\Clinic\Finance\Payable;
use App\Models\User;

class PayablePolicy
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
        return $user->hasRole('admin-clinica') || $user->can('visualizar-financeiro');
    }

    public function view(User $user, Payable $payable): bool
    {
        return $user->clinic_id === $payable->clinic_id && $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin-clinica') || $user->can('gerenciar-financeiro');
    }

    public function update(User $user, Payable $payable): bool
    {
        return $user->clinic_id === $payable->clinic_id && $this->create($user);
    }

    public function delete(User $user, Payable $payable): bool
    {
        return $user->clinic_id === $payable->clinic_id && $this->create($user);
    }
}
