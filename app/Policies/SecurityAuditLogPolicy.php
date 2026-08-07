<?php

namespace App\Policies;

use App\Models\User;

class SecurityAuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super-admin')
            || ($user->hasRole('admin-clinica') && $user->clinic_id !== null);
    }
}
