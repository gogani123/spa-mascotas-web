<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. REGLA DE CONTRASEÑAS SEGURAS (Lo que hicimos hace un rato)
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols();
        });

        // 2. REGLA DE EXPIRACIÓN DE TOKENS Y CORREOS (15 Minutos exactos)
        // Esto afecta al Link de Activación de cuenta:
        config(['auth.verification.expire' => 15]); 
        
        // Esto afecta al Link de "Olvidé mi contraseña" (Para darte doble seguridad en tu nota):
        config(['auth.passwords.users.expire' => 15]); 
    }
}