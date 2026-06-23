<?php

namespace App\Http\Controllers\Clinics\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Exibe as permissões de um papel.
     */
    public function index(Role $role): JsonResponse
    {
        $allPermissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('-', $permission->name);

            return count($parts) > 1 ? $parts[1] : 'outros';
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return response()->json([
            'role' => $role,
            'allPermissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Atualiza as permissões de um papel.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'message' => 'Permissões atualizadas com sucesso!',
        ]);
    }
}
