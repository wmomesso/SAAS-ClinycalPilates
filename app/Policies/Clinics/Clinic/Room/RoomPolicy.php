<?php

namespace App\Policies\Clinics\Clinic\Room;

use App\Models\Clinics\Clinic\Room\Room;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RoomPolicy
{
    /**
     * O Super Admin ignora as restrições.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->clinic_id !== null;
    }

    public function view(User $user, Room $room): bool
    {
        return $user->clinic_id === $room->clinic_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin-clinica');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasRole('admin-clinica') && $user->clinic_id === $room->clinic_id;
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->hasRole('admin-clinica') && $user->clinic_id === $room->clinic_id;
    }
}
