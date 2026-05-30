<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->orderBy('id')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'cedula_identidad' => $user->cedula_identidad ?? '—',
                    'name' => $user->name ?? '',
                    'apellidoPaterno' => $user->apellidoPaterno ?? '',
                    'apellidoMaterno' => $user->apellidoMaterno ?? '',
                    'username' => $user->username,
                    'email' => $user->email ?? '',
                    'celular' => $user->celular ?? '',
                    'activo' => (bool) ($user->activo ?? true),
                    'roles' => $user->getRoleNames(),
                ];
            });

        return Inertia::render('users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        // Solo super-admin y director pueden crear usuarios
        $roles = auth()->user()->hasRole('super-admin')
            ? Role::orderBy('name')->pluck('name')
            : Role::whereNotIn('name', ['super-admin'])->orderBy('name')->pluck('name');

        return Inertia::render('users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula_identidad' => 'required|string|max:20|unique:users,cedula_identidad',
            'name' => 'required|string|max:255',
            'apellidoPaterno' => 'nullable|string|max:100',
            'apellidoMaterno' => 'nullable|string|max:100',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'nullable|string|email|max:255|unique:users',
            'celular' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'apellidoPaterno' => $request->apellidoPaterno,
            'apellidoMaterno' => $request->apellidoMaterno,
            'cedula_identidad' => $request->cedula_identidad,
            'username' => $request->username,
            'celular' => $request->celular,
            'email' => $request->email ?? null,
            'password' => Hash::make($request->password),
            'activo' => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        // Solo super-admin y director pueden editar usuarios
        $roles = auth()->user()->hasRole('super-admin')
            ? Role::orderBy('name')->pluck('name')
            : Role::whereNotIn('name', ['super-admin'])->orderBy('name')->pluck('name');

        return Inertia::render('users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'cedula_identidad' => $user->cedula_identidad,
                'apellidoPaterno' => $user->apellidoPaterno,
                'apellidoMaterno' => $user->apellidoMaterno,
                'username' => $user->username,
                'email' => $user->email ?? '',
                'celular' => $user->celular,
                'activo' => (bool) $user->activo,
                'roles' => $user->getRoleNames(),
            ],
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellidoPaterno' => 'nullable|string|max:100',
            'apellidoMaterno' => 'nullable|string|max:100',
            'celular' => 'nullable|string|max:20',
            'cedula_identidad' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'apellidoPaterno' => $request->apellidoPaterno,
            'apellidoMaterno' => $request->apellidoMaterno,
            'cedula_identidad' => $request->cedula_identidad,
            'celular' => $request->celular,
            'username' => $request->username,
            'email' => $request->email ?? null,
        ]);

        // Actualizar rol
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Usuario '.$user->name.' actualizado con éxito');
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

        $user->update(['activo' => ! $user->activo]);

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
