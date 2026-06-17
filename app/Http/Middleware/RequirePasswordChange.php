<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            $defaultPassword = $user->cedula_identidad . ucfirst(strtolower($user->apellidoPaterno ?? ''));

            $tienePasswordDefault = Hash::check($defaultPassword, $user->password);

            if (
                $tienePasswordDefault &&
                !$request->routeIs('password.change') &&
                !$request->routeIs('password.change.update') &&
                !$request->routeIs('logout')
            ) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}
