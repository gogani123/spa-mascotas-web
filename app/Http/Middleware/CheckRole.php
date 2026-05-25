<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles (Permite recibir varios roles separados por coma)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Si el usuario no ha iniciado sesión, lo mandamos al login
        if (!$request->user()) {
            return redirect('login');
        }

        // Si el rol_id del usuario actual ESTÁ dentro de la lista permitida (ej: 1 y 2), pasa.
        if (in_array($request->user()->rol_id, $roles)) {
            return $next($request);
        }

        // Si no está en la lista, le denegamos el acceso (Error 403)
        abort(403, 'No tienes permisos para acceder a esta sección del sistema.');
    }
}