<x-guest-layout>
    <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg border dark:border-gray-700">
        
        <div class="mb-6 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            ¿Olvidaste tu contraseña? No hay problema. Simplemente indícanos tu dirección de correo electrónico y te enviaremos un enlace de recuperación que te permitirá elegir una nueva contraseña de forma segura.
        </div>

        <x-auth-session-status class="mb-4 text-green-500 font-bold" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div>
                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                    Correo Electrónico Registrado
                </label>
                <input id="email" 
                       class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required autofocus placeholder="ejemplo@correo.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4" href="{{ route('login') }}">
                    Volver al inicio
                </a>
                
                <button type="submit" class="inline-flex justify-center items-center px-4 py-3 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-bold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    ENVIAR ENLACE DE RECUPERACIÓN
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>