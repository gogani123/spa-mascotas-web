<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class AuthController extends Controller
{
    /**
     * LOGIN POR TOKEN: Valida Email, Password y Código 2FA (Google Authenticator)
     */
    public function login(Request $request)
    {
        // 1. Validar que vengan los datos obligatorios
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'code' => 'required|digits:6', // Requerimos los 6 dígitos del celular
        ]);

        // 2. Buscar al usuario en PostgreSQL por su correo
        $user = User::where('email', $request->email)->first();

        // 3. Verificar criptográficamente la contraseña
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => '🛑 Credenciales incorrectas. No se pudo generar el token de seguridad.'
            ], 401);
        }

        // 4. VERIFICACIÓN DEL SEGUNDO FACTOR (2FA)
        // Comprobamos si el usuario tiene una llave secreta de Google Authenticator configurada
        if ($user->two_factor_secret) {
            $google2fa = new Google2FA();
            
            // Validamos el código de 6 dígitos contra su llave secreta
            $valido = $google2fa->verifyKey($user->two_factor_secret, $request->code);

            if (!$valido) {
                return response()->json([
                    'status' => 'error',
                    'message' => '🛑 El código de verificación de Google Authenticator es incorrecto o ya expiró.'
                ], 422);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => '🛑 Esta cuenta requiere configuración 2FA activa para acceder por API.'
            ], 403);
        }

        // 5. Generar el Token único seguro si todo es correcto
        $token = $user->createToken('token_operativo_spa')->plainTextToken;

        // 6. Responder con el éxito y el Token
        return response()->json([
            'status' => 'success',
            'message' => '🔑 ¡Autenticación 2FA exitosa! Token generado correctamente.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'rol_id' => $user->rol_id
            ]
        ], 200);
    }

    /**
     * LOGOUT POR TOKEN: Revoca e invalida el token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => '🔒 Sesión por token invalidada y destruida de forma segura.'
        ], 200);
    }
}