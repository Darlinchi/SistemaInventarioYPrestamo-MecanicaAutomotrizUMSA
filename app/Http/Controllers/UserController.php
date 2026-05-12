<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'super-admin'))
            ->get()
            ->map(function ($user) {
                return [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username,
                    'email'    => $user->email,
                    'activo'   => (bool) $user->activo,
                    'roles'    => $user->getRoleNames(),
                ];
            });

        return Inertia::render('users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        // Solo super-admin y director pueden crear usuarios
        $roles = Role::whereNotIn('name', ['super-admin'])->pluck('name');

        return Inertia::render('users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'username'              => 'required|string|max:255|unique:users',
            'email'                 => 'nullable|string|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'role'                  => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email ?? null,
            'password' => Hash::make($request->password),
            'activo'   => true,
        ]);

        $user->assignRole($request->role);

        return redirect('/dashboard/usuarios')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        // Solo super-admin y director pueden editar usuarios
        $roles = Role::whereNotIn('name', ['super-admin'])->pluck('name');

        return Inertia::render('users/Edit', [
            'user'  => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email ?? '',
                'activo'   => (bool) $user->activo,
                'roles'    => $user->getRoleNames(),
            ],
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email'    => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role'     => 'required|exists:roles,name',
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email ?? null,
        ]);

        // Actualizar rol
        $user->syncRoles([$request->role]);

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    // ── Cambiar contraseña desde Edit ─────────────────────────────
    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    // ── Resetear contraseña a "12345678" ──────────────────────────
    public function resetPassword(User $user)
    {
        $user->update([
            'password' => Hash::make('12345678'),
        ]);

        return back()->with('success', 'Contraseña reseteada a: 12345678');
    }

    // ── Toggle habilitar / deshabilitar ───────────────────────────
    public function toggleStatus(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes deshabilitar tu propia cuenta.');
        }

        $user->update(['activo' => !$user->activo]);

        $estado = $user->activo ? 'habilitado' : 'deshabilitado';
        return back()->with('success', "Usuario {$estado} correctamente.");
    }

    // ── Eliminar usuario ──────────────────────────────────────────
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
