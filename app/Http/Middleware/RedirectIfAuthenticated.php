<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                return match ($user->tipo_usuario) {
                    'root'           => redirect('/root'),
                    'administrador'  => redirect('/admin'),
                    'tecnico'        => redirect('/tecnico'),
                    'jefe_operativo' => redirect('/jefe_operativo'),
                    'capturista'     => redirect('/capturista'),
                    default          => redirect('/'),
                };
            }
        }

        return $next($request);
    }
}
