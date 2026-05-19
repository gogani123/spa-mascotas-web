<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Registrar Personal Interno
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    
                    <<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <div class="space-y-4">
        <h3 class="text-lg font-bold text-indigo-500 border-b border-gray-700 pb-2">Datos Personales y de Acceso</h3>
        
        <div>
            <label class="block text-sm font-medium text-gray-300">Nombre Completo</label>
            <input type="text" name="name" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300">Carnet de Identidad (CI)</label>
            <input type="text" name="ci" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md" placeholder="Ej: 1234567" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300">Correo Institucional</label>
            <input type="email" name="email" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300">Contraseña Temporal</label>
            <div class="relative mt-1">
                <input id="password" type="password" name="password" class="w-full bg-gray-900 border-gray-700 text-white rounded-md pr-10" required>
                <button type="button" onclick="togglePassword('password', 'eye-icon-temp')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white focus:outline-none">
                    <svg id="eye-icon-temp" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        <h3 class="text-lg font-bold text-indigo-500 border-b border-gray-700 pb-2">Información Laboral y Contacto</h3>
        
        <div>
            <label class="block text-sm font-medium text-gray-300">Teléfono / Celular</label>
            <input type="text" name="telefono" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md" placeholder="Ej: 77777777" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300">Puesto / Rol</label>
            <select name="rol_id" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md">
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300">Especialidad (Si es Groomer)</label>
            <input type="text" name="especialidad" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md" placeholder="Ej: Cortes de raza, Tintura">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300">Turno Asignado</label>
            <select name="turno" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md">
                <option value="Mañana">Mañana (08:00 - 12:00)</option>
                <option value="Tarde">Tarde (14:00 - 18:00)</option>
                <option value="Completo">Tiempo Completo</option>
            </select>
        </div>
    </div>
</div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="px-10 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-lg transition">
                            Registrar en el Sistema
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        if (typeof togglePassword !== 'function') {
            function togglePassword(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
                } else {
                    input.type = 'password';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                }
            }
        }
    </script>
</x-app-layout>