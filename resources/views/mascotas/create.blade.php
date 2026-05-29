<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Registrar Nueva Mascota
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                
                <div class="mb-8 text-center">
                    <h3 class="text-2xl font-bold text-indigo-400">Datos de tu Peludito</h3>
                    <p class="text-gray-400 mt-2">Completa la ficha técnica para que podamos brindarle el mejor servicio en el Spa.</p>
                </div>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-900/80 border border-red-700 text-red-200 rounded-md font-medium text-sm">
                        <p class="font-bold mb-1">❌ Por favor corrige los siguientes errores:</p>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('mascotas.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                        <div class="mb-8 bg-gray-900/50 p-4 rounded-md border border-indigo-500/30">
                            <label class="block text-sm font-bold text-indigo-300">Asignar a un Cliente (Dueño) *</label>
                            <select name="user_id" required class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 text-sm">
                                <option value="" disabled selected>-- Elige al usuario dueño de la mascota --</option>
                                @foreach(\App\Models\User::where('rol_id', 4)->orderBy('name', 'asc')->get() as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->name }} ({{ $cliente->email }})</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1">Como personal del Spa, debes elegir a qué cliente le pertenece esta mascota.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <div class="space-y-5">
                            <h4 class="text-lg font-bold text-gray-300 border-b border-gray-700 pb-2">Información Principal</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Nombre de la Mascota *</label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}" class="w-full mt-1 bg-gray-900 border-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Especie *</label>
                                    <select name="especie" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="" disabled selected>Selecciona...</option>
                                        <option value="Perro" {{ old('especie') == 'Perro' ? 'selected' : '' }}>Perro</option>
                                        <option value="Gato" {{ old('especie') == 'Gato' ? 'selected' : '' }}>Gato</option>
                                        <option value="Otro" {{ old('especie') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Raza (Opcional)</label>
                                    <input type="text" name="raza" value="{{ old('raza') }}" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Tamaño *</label>
                                    <select name="tamano" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="" disabled selected>Selecciona...</option>
                                        <option value="Pequeño" {{ old('tamano') == 'Pequeño' ? 'selected' : '' }}>Pequeño/a</option>
                                        <option value="Mediano" {{ old('tamano') == 'Mediano' ? 'selected' : '' }}>Mediano/a (+10% tiempo)</option>
                                        <option value="Grande" {{ old('tamano') == 'Grande' ? 'selected' : '' }}>Grande (+15% tiempo)</option>
                                        <option value="Gigante" {{ old('tamano') == 'Gigante' ? 'selected' : '' }}>Gigante / Raza Compleja (+30% tiempo)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Fecha de Nacimiento *</label>
                                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="w-full mt-1 bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h4 class="text-lg font-bold text-gray-300 border-b border-gray-700 pb-2">Salud y Comportamiento</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Comportamiento *</label>
                                <select name="comportamiento" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="" disabled selected>¿Cómo se comporta normalmente?</option>
                                    <option value="Tranquilo" {{ old('comportamiento') == 'Tranquilo' ? 'selected' : '' }}>Tranquilo / Normal</option>
                                    <option value="Nervioso" {{ old('comportamiento') == 'Nervioso' ? 'selected' : '' }}>Nervioso / Asustadizo (+20 min extra)</option>
                                    <option value="Agresivo" {{ old('comportamiento') == 'Agresivo' ? 'selected' : '' }}>Agresivo / Reactivo (+20 min extra)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Alergias o médicas (Opcional)</label>
                                <textarea name="alergias" rows="2" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('alergias') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-amber-500">Carnet de Vacunas (PDF, JPG, PNG)</label>
                                <input type="file" name="carnet_vacunas" accept=".pdf, .jpg, .jpeg, .png" class="w-full mt-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md">
                            </div>
                        </div>

                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-lg transition transform hover:scale-105">
                            REGISTRAR MASCOTA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>