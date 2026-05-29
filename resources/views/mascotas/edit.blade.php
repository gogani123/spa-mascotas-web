<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Modificar Ficha de la Mascota: {{ $mascota->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                
                <div class="mb-8 text-center">
                    <h3 class="text-2xl font-bold text-indigo-400">Actualizar Datos Clínicos / Estéticos</h3>
                    <p class="text-gray-400 mt-2">Modifica los parámetros necesarios. Estos cambios afectarán el cálculo de tiempos en la agenda.</p>
                </div>

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-900/80 border border-red-700 text-red-200 rounded-md font-medium text-sm">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('mascotas.update', $mascota->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                        <div class="mb-8 bg-gray-900/50 p-4 rounded-md border border-indigo-500/30">
                            <label class="block text-sm font-bold text-indigo-300">Modificar Cliente (Dueño) *</label>
                            <select name="user_id" required class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2 text-sm">
                                @foreach(\App\Models\User::where('rol_id', 4)->get() as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $mascota->user_id == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->name }} ({{ $cliente->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-5">
                            <h4 class="text-lg font-bold text-gray-300 border-b border-gray-700 pb-2">Información Principal</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Nombre de la Mascota *</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $mascota->nombre) }}" class="w-full mt-1 bg-gray-900 border-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 rounded-md shadow-sm" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Especie *</label>
                                    <select name="especie" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="Perro" {{ old('especie', $mascota->especie) == 'Perro' ? 'selected' : '' }}>Perro</option>
                                        <option value="Gato" {{ old('especie', $mascota->especie) == 'Gato' ? 'selected' : '' }}>Gato</option>
                                        <option value="Otro" {{ old('especie', $mascota->especie) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Raza</label>
                                    <input type="text" name="raza" value="{{ old('raza', $mascota->raza) }}" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Tamaño *</label>
                                    <select name="tamano" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                        <option value="Pequeño" {{ old('tamano', $mascota->tamano) == 'Pequeño' ? 'selected' : '' }}>Pequeño/a</option>
                                        <option value="Mediano" {{ old('tamano', $mascota->tamano) == 'Mediano' ? 'selected' : '' }}>Mediano/a</option>
                                        <option value="Grande" {{ old('tamano', $mascota->tamano) == 'Grande' ? 'selected' : '' }}>Grande</option>
                                        <option value="Gigante" {{ old('tamano', $mascota->tamano) == 'Gigante' ? 'selected' : '' }}>Gigante</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-400">Fecha de Nacimiento *</label>
                                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $mascota->fecha_nacimiento) }}" class="w-full mt-1 bg-gray-900 border-gray-700 text-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <h4 class="text-lg font-bold text-gray-300 border-b border-gray-700 pb-2">Salud y Comportamiento</h4>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Comportamiento *</label>
                                <select name="comportamiento" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="Tranquilo" {{ old('comportamiento', $mascota->comportamiento) == 'Tranquilo' ? 'selected' : '' }}>Tranquilo / Normal</option>
                                    <option value="Nervioso" {{ old('comportamiento', $mascota->comportamiento) == 'Nervioso' ? 'selected' : '' }}>Nervioso / Asustadizo</option>
                                    <option value="Agresivo" {{ old('comportamiento', $mascota->comportamiento) == 'Agresivo' ? 'selected' : '' }}>Agresivo / Reactivo</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-400">Alergias o contraindicaciones</label>
                                <textarea name="alergias" rows="2" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('alergias', $mascota->alergias) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-amber-500">Actualizar Carnet de Vacunas (Opcional)</label>
                                <input type="file" name="carnet_vacunas" accept=".pdf, .jpg, .jpeg, .png" class="w-full mt-1 text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md">
                                @if($mascota->carnet_vacunas)
                                    <p class="text-[11px] text-emerald-400 mt-1">✓ Ya existe un carnet registrado en el servidor.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex justify-end gap-4">
                        <a href="{{ route('mascotas.index') }}" class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-md transition text-sm">
                            Cancelar
                        </a>
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-lg transition transform hover:scale-105 text-sm">
                            GUARDAR CAMBIOS
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>