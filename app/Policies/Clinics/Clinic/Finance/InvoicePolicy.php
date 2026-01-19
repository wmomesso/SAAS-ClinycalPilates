<?php

namespace App\Policies\Clinics\Clinic\Finance;

use App\Models\Clinics\Clinic\Finance\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ver-relatorios-financeiros') || $user->hasRole('admin-clinica');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->clinic_id === $invoice->clinic_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin-clinica') || $user->hasRole('recepcionista');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->clinic_id === $invoice->clinic_id &&
            ($user->hasRole('admin-clinica') || $user->hasRole('recepcionista'));
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin-clinica') && $user->clinic_id === $invoice->clinic_id;
    }
}
