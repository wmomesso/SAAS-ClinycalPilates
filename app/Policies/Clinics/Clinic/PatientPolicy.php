<?php

namespace App\Policies\Clinics\Clinic;

use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * O Super Admin pode tudo.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determina se o utilizador pode ver a lista de pacientes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('gerenciar-pacientes') || $user->hasRole('profissional');
    }

    /**
     * Determina se o utilizador pode ver um paciente específico.
     */
    public function view(User $user, Patient $patient): bool
    {
        // O paciente deve pertencer à mesma clínica do utilizador
        return $user->clinic_id === $patient->clinic_id;
    }

    /**
     * Determina se o utilizador pode criar pacientes.
     */
    public function create(User $user): bool
    {
        return $user->can('gerenciar-pacientes');
    }

    /**
     * Determina se o utilizador pode atualizar o paciente.
     */
    public function update(User $user, Patient $patient): bool
    {
        return ($user->can('gerenciar-pacientes') || $user->hasRole('profissional'))
            && $user->clinic_id === $patient->clinic_id;
    }

    /**
     * Determina se o utilizador pode apagar o paciente.
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasRole('admin-clinica') && $user->clinic_id === $patient->clinic_id;
    }
}
