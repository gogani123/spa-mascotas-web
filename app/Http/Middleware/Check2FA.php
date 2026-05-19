<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Check2FA
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Verificamos si hay alguien logueado y si su Rol es 1 (Administrador)
        if ($user && $user->rol_id == 1) {
            
            // 2. Si es Admin pero aún no tiene el 2FA activado (su primera vez)
            if (!$user->two_factor_enabled) {
                // Lo enviamos a la pantalla para escanear el Código QR
                if (!$request->is('2fa/setup') && !$request->is('2fa/verify') && !$request->is('logout')) {
                    return redirect()->route('2fa.setup');
                }
            } 
            // 3. Si ya escaneó el QR antes, verificamos si ya ingresó la clave en esta sesión
            else if (!session('2fa_verified')) {
                // Lo enviamos a la pantalla para ingresar los 6 dígitos de su celular
                if (!$request->is('2fa/setup') && !$request->is('2fa/verify') && !$request->is('logout')) {
                    return redirect()->route('2fa.index');
                }
            }
        }

        // Si es Cliente (rol 4), Recepción, o si el Admin ya puso su código, pasa libremente.
        return $next($request);
    }
}