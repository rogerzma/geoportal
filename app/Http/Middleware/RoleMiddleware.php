<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (strtolower((string)Auth::user()->tipo_usuario) !== strtolower((string)$role)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }

        return $next($request);
    }
}
