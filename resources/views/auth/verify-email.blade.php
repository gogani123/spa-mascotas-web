<x-guest-layout>
    <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg border dark:border-gray-700">
        
        <div class="mb-6 text-center">
            <div class="flex justify-center mb-4">
                <span class="text-4xl">🔑</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 dark:text-indigo-400">Verifica tu Correo Electrónico</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 leading-relaxed">
                Hemos enviado un código alfanumérico de 6 dígitos en mayúsculas a tu correo. Por favor, búscalo e ingrésalo a continuación para activar tu cuenta.
            </p>
        </div>

        <!-- Bloque de Alertas por Errores -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-900/50 border border-red-700 text-red-200 rounded text-xs font-bold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 p-3 bg-green-900/50 border border-green-700 text-green-300 rounded text-xs font-bold text-center">
                ✨ Se ha enviado un nuevo código de verificación a tu correo.
            </div>
        @endif

        <!-- Formulario para ingresar el código OTP -->
        <form method="POST" action="{{ route('verification.verify_code') }}" class="space-y-5">
            @csrf
            <div>
                <label for="codigo" class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider text-center mb-2">
                    Código de Verificación (6 caracteres)
                </label>
                <input type="text" id="codigo" name="codigo" required autocomplete="off" maxlength="6"
                       placeholder="EJ: XY7A9B"
                       class="w-full text-center font-bold text-xl uppercase tracking-widest bg-gray-100 dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm p-3">
            </div>

            <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded shadow transition text-sm transform hover:scale-[1.01]">
                Validar Código y Entrar
            </button>
        </form>

        <div class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
            <!-- Botón para Reenviar Código -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="text-xs text-gray-600 dark:text-gray-400 hover:text-indigo-400 underline transition focus:outline-none">
                    Reenviar código
                </button>
            </form>

            <!-- Botón Cerrar Sesión -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-red-600 dark:text-red-400 hover:text-red-300 underline transition focus:outline-none">
                    Cerrar Sesión
                </button>
            </form>
        </div>

    </div>
</x-guest-layout>