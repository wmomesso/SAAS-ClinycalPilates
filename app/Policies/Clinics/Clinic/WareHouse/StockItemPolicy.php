<?php

namespace App\Policies\Clinics\Clinic\WareHouse;

use App\Models\Clinics\Clinic\WareHouse\StockItem;
use App\Models\User;

class StockItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin-clinica')
            || $user->can('visualizar-lista-compras-clinica')
            || $user->can('gerenciar-lista-compras-clinica');
    }

    public function view(User $user, StockItem $stockItem): bool
    {
        return $user->clinic_id === $stockItem->clinic_id && $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin-clinica') || $user->can('gerenciar-lista-compras-clinica');
    }

    public function update(User $user, StockItem $stockItem): bool
    {
        return $user->clinic_id === $stockItem->clinic_id && $this->create($user);
    }

    public function delete(User $user, StockItem $stockItem): bool
    {
        return $this->update($user, $stockItem);
    }
}
