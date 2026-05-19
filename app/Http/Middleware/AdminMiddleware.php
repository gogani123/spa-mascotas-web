<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Revisamos si el usuario ha iniciado sesión Y si su rol es 1 (Admin)
        if (auth()->check() && auth()->user()->rol_id == 1) {
            return $next($request); // Déjalo pasar
        }
        
        // Si no es admin, lo devolvemos al panel principal
        return redirect('/dashboard');
    }
}