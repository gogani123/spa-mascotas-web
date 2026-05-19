<x-guest-layout>
    <div class="text-center mb-4 text-sm text-gray-600 dark:text-gray-400">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Código de Seguridad</h2>
        <p>Abre tu aplicación Google Authenticator e ingresa el código de 6 dígitos.</p>
    </div>

    <form method="POST" action="{{ route('2fa.verify') }}">
        @csrf

        <div class="mt-4">
            <x-input-label for="one_time_password" value="Código de 6 dígitos" class="text-center block" />

            <x-text-input id="one_time_password" class="block mt-1 w-full text-center tracking-widest text-2xl font-mono" 
                            type="text" 
                            name="one_time_password" 
                            required autofocus autocomplete="off" />

            <x-input-error :messages="$errors->get('one_time_password')" class="mt-2 text-center" />
        </div>

        <div class="flex items-center justify-center mt-6">
            <x-primary-button class="w-full justify-center">
                Verificar y Entrar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>