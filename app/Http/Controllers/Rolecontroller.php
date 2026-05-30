<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->orderBy('name')
            ->get()
            ->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name'),
                'users_count' => $role->users()->count(),
            ]);

        // Agrupamos permisos por módulo para mostrarlos organizados en la UI
        $permissionGroups = Permission::orderBy('name')
            ->get()
            ->groupBy(fn ($p) => explode('.', $p->name)[0])
            ->map(fn ($group) => $group->pluck('name')->values());

        return Inertia::render('users/roles/Index', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', "Rol '{$role->name}' creado correctamente.");
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super-admin') {
            return back()->with('error', 'El rol super-admin no puede modificarse.');
        }

        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,'.$role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        // Limpiar caché → cambios inmediatos para todos los usuarios
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return back()->with('success', "Rol '{$role->name}' actualizado. Cambios aplicados inmediatamente.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['super-admin', 'encargado', 'director'])) {
            return back()->with('error', 'Los roles base del sistema no pueden eliminarse.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "No se puede eliminar: {$role->users()->count()} usuario(s) tienen este rol asignado.");
        }

        $role->delete();

        return back()->with('success', 'Rol eliminado correctamente.');
    }
}
