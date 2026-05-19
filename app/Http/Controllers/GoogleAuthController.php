<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    // 1. Envía al usuario a la pantalla de Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Recibe los datos del usuario cuando Google lo devuelve
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Buscamos si el usuario ya existe en nuestra base de datos
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Si no existe, lo registramos automáticamente como Cliente (Rol 4)
                $rolCliente = DB::table('roles')->where('nombre', 'Cliente')->first();

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)), 
                    'rol_id' => $rolCliente ? $rolCliente->id : 4,
                    'email_verified_at' => now(), // Verificado al instante
                ]);
            } else {
                // MAGIA EXTRA: Si el usuario ya existía pero no había verificado su correo, 
                // como entró con Google, lo damos por verificado automáticamente.
                if ($user->email_verified_at === null) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            }

            // Iniciamos sesión y lo mandamos al Dashboard
            Auth::login($user);
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Ocurrió un error al intentar iniciar sesión con Google.']);
        }
    }
}