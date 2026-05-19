<x-guest-layout>
    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg border dark:border-gray-700">
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <h2 class="text-2xl font-bold text-gray-800 dark:text-white text-center mb-6">Crea tu cuenta en Spa Mascotas</h2>

            <div>
                <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nombre Completo</label>
                <input id="name" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" type="text" name="name" value="{{ old('name') }}" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Correo Electrónico</label>
                    <input id="email" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" type="email" name="email" value="{{ old('email') }}" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div>
                    <label for="ci" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Cédula de Identidad (CI)</label>
                    <input id="ci" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" type="text" name="ci" value="{{ old('ci') }}" required placeholder="Ej: 1234567" />
                    <x-input-error :messages="$errors->get('ci')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="telefono" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Teléfono / WhatsApp</label>
                    <input id="telefono" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" type="text" name="telefono" value="{{ old('telefono') }}" required />
                    <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                </div>
                <div>
                    <label for="direccion" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Dirección</label>
                    <input id="direccion" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" type="text" name="direccion" value="{{ old('direccion') }}" required />
                    <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700 my-4">

            <div>
                <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Contraseña Segura</label>
                <div class="relative mt-1">
                    <input id="password" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm pr-10" 
                           type="password" name="password" required autocomplete="new-password" />
                    
                    <button type="button" onclick="togglePassword('password', 'eye-icon-reg1')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-300 focus:outline-none">
                        <svg id="eye-icon-reg1" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-3 bg-gray-100 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-600 dark:text-gray-400">Seguridad: <span id="strength-text" class="font-bold uppercase">Ninguna</span></span>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2 dark:bg-gray-600">
                        <div id="strength-bar" class="bg-red-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
                <div class="relative mt-1">
                    <input id="password_confirmation" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm pr-10" 
                           type="password" name="password_confirmation" required />
                    
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-reg2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-300 focus:outline-none">
                        <svg id="eye-icon-reg2" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-center mt-6">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4" href="{{ route('login') }}">
                    ¿Ya tienes cuenta?
                </a>
                <button type="submit" class="inline-flex justify-center items-center px-4 py-3 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-bold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    REGISTRAR CUENTA
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');

            passwordInput.addEventListener('input', function() {
                const val = passwordInput.value;
                let strength = 0;
                if (val.length >= 8) strength += 25;
                if (val.match(/[A-Z]/) && val.match(/[a-z]/)) strength += 25;
                if (val.match(/[0-9]/)) strength += 25;
                if (val.match(/[^a-zA-Z0-9]/)) strength += 25;

                strengthBar.style.width = strength + '%';
                if (strength === 0) { strengthText.textContent = 'Ninguna'; strengthText.className = 'text-gray-500'; }
                else if (strength <= 50) { strengthBar.className = 'bg-red-500 h-2 rounded-full'; strengthText.textContent = 'Débil'; strengthText.className = 'text-red-500 font-bold'; }
                else if (strength === 75) { strengthBar.className = 'bg-yellow-500 h-2 rounded-full'; strengthText.textContent = 'Aceptable'; strengthText.className = 'text-yellow-500 font-bold'; }
                else { strengthBar.className = 'bg-green-500 h-2 rounded-full'; strengthText.textContent = '¡Fuerte!'; strengthText.className = 'text-green-500 font-bold'; }
            });
        });
    </script>
</x-guest-layout>