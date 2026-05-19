<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Verificamos si tiene sesión activa y si su rol_id coincide con el exigido
        if (!Auth::check() || Auth::user()->rol_id != $role) {
            return redirect('/dashboard'); // Lo expulsamos si intenta entrar a la fuerza
        }

        return $next($request);
    }
}