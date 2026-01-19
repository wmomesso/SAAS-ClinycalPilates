<?php

namespace App\Policies\Clinics\Clinic\Appointment;

use App\Models\Clinics\Clinic\Appointment\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * O Super Admin pode ignorar as restrições de tenant.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determina se o usuário pode ver a lista de agendamentos.
     */
    public function viewAny(User $user): bool
    {
        // Todos os usuários da clínica podem ver a agenda.
        return $user->clinic_id !== null;
    }

    /**
     * Determina se o usuário pode ver um agendamento específico.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return $user->clinic_id === $appointment->clinic_id;
    }

    /**
     * Determina se o usuário pode criar agendamentos.
     */
    public function create(User $user): bool
    {
        return $user->can('gerenciar-todos-agendamentos') || $user->hasRole('profissional') || $user->hasRole('recepcionista');
    }

    /**
     * Determina se o usuário pode atualizar o agendamento.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Bloqueia acesso cruzado entre clínicas.
        if ($user->clinic_id !== $appointment->clinic_id) {
            return false;
        }

        // Profissionais podem editar seus próprios agendamentos, admins podem editar todos da clínica.
        return $user->hasRole('admin-clinica') ||
            $user->hasRole('recepcionista') ||
            ($user->hasRole('profissional') && $user->id === $appointment->professional_id);
    }

    /**
     * Determina se o usuário pode deletar o agendamento.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasRole('admin-clinica') && $user->clinic_id === $appointment->clinic_id;
    }
}
