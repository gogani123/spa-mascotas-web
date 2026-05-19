<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    // 1. Pantalla para mostrar el Código QR (Solo la primera vez)
    public function setup()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        // Generamos la llave secreta si no la tiene en la base de datos
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        // Creamos la URL para que Google Authenticator la entienda
        $QR_Image = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return view('auth.2fa_setup', ['QR_Image' => $QR_Image, 'secret' => $user->two_factor_secret]);
    }

    // 2. Pantalla para pedir los 6 dígitos (Cada vez que inicia sesión)
    public function index()
    {
        return view('auth.2fa_verify');
    }

    // 3. Validar matemáticamente los 6 dígitos que envía el celular
    public function verify(Request $request)
    {
        $request->validate(['one_time_password' => 'required|numeric']);

        $user = Auth::user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->one_time_password);

        if ($valid) {
            // Si es correcto, activamos el 2FA permanentemente y aprobamos la sesión
            $user->two_factor_enabled = true;
            $user->save();
            
            session(['2fa_verified' => true]);
            return redirect()->route('dashboard');
        }

        // Si se equivoca de código, lo devolvemos con un error
        return back()->withErrors(['one_time_password' => 'El código ingresado es incorrecto o ha expirado.']);
    }
}