<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 border-b border-gray-700 pb-2">
            Información del Perfil
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Aquí puedes visualizar tus datos personales registrados en el sistema.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nombre Completo" />
            <x-text-input id="name" name="name" type="text" class="edit-input mt-1 block w-full bg-gray-900 border-gray-700 text-gray-400 cursor-not-allowed" :value="old('name', $user->name)" required disabled />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo Institucional" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-500 cursor-not-allowed opacity-70" :value="old('email', $user->email)" required disabled />
        </div>

        <div>
            <x-input-label for="ci" value="Carnet de Identidad (CI)" class="text-indigo-400 font-bold" />
            <x-text-input id="ci" name="ci" type="text" class="edit-input mt-1 block w-full bg-gray-900 border-gray-700 text-gray-400 cursor-not-allowed" :value="old('ci', $user->ci)" required disabled />
            <x-input-error class="mt-2" :messages="$errors->get('ci')" />
        </div>

        <div>
            <x-input-label for="telefono" value="Número de Teléfono / Celular" class="text-indigo-400 font-bold" />
            <x-text-input id="telefono" name="telefono" type="text" class="edit-input mt-1 block w-full bg-gray-900 border-gray-700 text-gray-400 cursor-not-allowed" :value="old('telefono', $user->telefono)" required disabled />
            <x-input-error class="mt-2" :messages="$errors->get('telefono')" />
        </div>

        <div class="flex items-center gap-4 mt-8">
            <button type="button" id="btn-modificar" onclick="activarEdicion()" class="px-6 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-md shadow-lg transition">
                MODIFICAR DATOS
            </button>

            <button type="submit" id="btn-guardar" class="hidden px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-lg transition">
                GUARDAR CAMBIOS
            </button>

            <button type="button" id="btn-cancelar" onclick="window.location.reload()" class="hidden px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-md shadow-lg transition">
                CANCELAR
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm text-green-400 font-bold">¡Datos actualizados correctamente!</p>
            @endif
        </div>
    </form>

    <script>
        function activarEdicion() {
            // 1. Buscamos todos los inputs que queremos habilitar
            const inputs = document.querySelectorAll('.edit-input');
            
            inputs.forEach(input => {
                input.disabled = false; // Quita el bloqueo
                input.classList.remove('text-gray-400', 'cursor-not-allowed'); // Quita estilo de bloqueo
                input.classList.add('text-white', 'border-indigo-500'); // Pone estilo de edición
            });

            // 2. Cambiamos los botones
            document.getElementById('btn-modificar').classList.add('hidden');
            document.getElementById('btn-guardar').classList.remove('hidden');
            document.getElementById('btn-cancelar').classList.remove('hidden');
        }
    </script>
</section>