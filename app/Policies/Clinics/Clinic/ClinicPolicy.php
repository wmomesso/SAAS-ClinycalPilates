<?php

namespace App\Policies\Clinics\Clinic;

use App\Models\Clinics\Clinic\Clinic;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClinicPolicy
{
    /**
     * O método 'before' é executado antes de qualquer outra verificação.
     * Se retornar true, a autorização é concedida imediatamente.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determina se o usuário pode listar todas as clínicas.
     */
    public function viewAny(User $user): bool
    {
        // Apenas o super-admin (tratado no 'before') pode listar tudo.
        return false;
    }

    /**
     * Determina se o usuário pode visualizar os dados da clínica.
     */
    public function view(User $user, Clinic $clinic): bool
    {
        // Um usuário pode ver a clínica se ele pertencer a ela.
        return $user->clinic_id === $clinic->id;
    }

    /**
     * Determina se o usuário pode criar modelos.
     */
    public function create(User $user): bool
    {
        // Criação normalmente via registro SAAS ou Super Admin.
        return false;
    }

    /**
     * Determina se o usuário pode atualizar a clínica.
     */
    public function update(User $user, Clinic $clinic): bool
    {
        // O usuário pode atualizar se ele for o dono da clínica (owner_id).
        return $user->id === $clinic->owner_id;
    }

    /**
     * Determina se o usuário pode deletar a clínica.
     */
    public function delete(User $user, Clinic $clinic): bool
    {
        // Apenas o dono pode deletar.
        return $user->id === $clinic->owner_id;
    }

    /**
     * Determina se o usuário pode restaurar o modelo.
     */
    public function restore(User $user, Clinic $clinic): bool
    {
        return false;
    }

    /**
     * Determina se o usuário pode deletar permanentemente o modelo.
     */
    public function forceDelete(User $user, Clinic $clinic): bool
    {
        return false;
    }
}
