@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado con info de mascota -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">🐾 {{ $mascota->nombre }}</h1>
                <p class="text-lg">{{ $mascota->raza }} • {{ $mascota->tamaño }} • {{ $cita->servicio->nombre }}</p>
                <p class="text-sm mt-2">Cliente: {{ $cita->cliente->name }} | Teléfono: {{ $cita->cliente->telefono ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <p class="text-4xl font-bold">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m') }}</p>
                <p class="text-lg">{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</p>
                <span class="inline-block mt-2 px-4 py-1 bg-white text-green-600 rounded-full font-bold">
                    {{ ucfirst($cita->estado) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Navegación de tabs -->
    <div class="bg-white rounded-lg shadow-md mb-6 border-b border-gray-200">
        <div class="flex overflow-x-auto">
            <a href="#estado" onclick="activeTab('estado')" 
               class="tab-button px-6 py-4 font-semibold text-gray-700 border-b-2 border-transparent hover:border-blue-500 transition active">
               📋 Estado de Entrada
            </a>
            <a href="#checklist" onclick="activeTab('checklist')" 
               class="tab-button px-6 py-4 font-semibold text-gray-700 border-b-2 border-transparent hover:border-blue-500 transition">
               ✅ Checklist
            </a>
            <a href="#fotos" onclick="activeTab('fotos')" 
               class="tab-button px-6 py-4 font-semibold text-gray-700 border-b-2 border-transparent hover:border-blue-500 transition">
               📷 Fotos
            </a>
            <a href="#recomendaciones" onclick="activeTab('recomendaciones')" 
               class="tab-button px-6 py-4 font-semibold text-gray-700 border-b-2 border-transparent hover:border-blue-500 transition">
               💡 Recomendaciones
            </a>
        </div>
    </div>

    <!-- TAB 1: ESTADO DE ENTRADA -->
    <div id="estado" class="tab-content bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">📋 Estado de Entrada de la Mascota</h2>
        
        <form action="{{ route('groomer.ficha.guardar', $cita->id) }}" method="POST" class="space-y-6">
            @csrf

            <!-- Estado físico -->
            <div>
                <label for="estado_ingreso" class="block text-lg font-semibold text-gray-700 mb-2">
                    🔍 Condición Física al Ingreso
                </label>
                <textarea name="estado_ingreso" id="estado_ingreso" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Describe: nudos, heridas, pulgas, suciedad, comportamiento, etc."
                          required>{{ $ficha->estado_ingreso ?? '' }}</textarea>
                @error('estado_ingreso')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Temperamento -->
            <div>
                <label for="temperamento" class="block text-lg font-semibold text-gray-700 mb-2">
                    😊 Temperamento Observado
                </label>
                <select name="temperamento" id="temperamento" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        required>
                    <option value="">Selecciona un temperamento...</option>
                    <option value="tranquilo" {{ ($ficha->temperamento ?? '') === 'tranquilo' ? 'selected' : '' }}>
                        😴 Tranquilo
                    </option>
                    <option value="nervioso" {{ ($ficha->temperamento ?? '') === 'nervioso' ? 'selected' : '' }}>
                        😰 Nervioso
                    </option>
                    <option value="agresivo" {{ ($ficha->temperamento ?? '') === 'agresivo' ? 'selected' : '' }}>
                        😠 Agresivo
                    </option>
                    <option value="inquieto" {{ ($ficha->temperamento ?? '') === 'inquieto' ? 'selected' : '' }}>
                        🤪 Inquieto
                    </option>
                </select>
                @error('temperamento')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Observaciones -->
            <div>
                <label for="observaciones" class="block text-lg font-semibold text-gray-700 mb-2">
                    📝 Observaciones Técnicas
                </label>
                <textarea name="observaciones" id="observaciones" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="Comentarios técnicos relevantes durante el servicio">{{ $ficha->observaciones ?? '' }}</textarea>
            </div>

            <!-- Datos de la mascota (información) -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-semibold text-gray-800 mb-3">Información Registrada de la Mascota</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 uppercase font-bold">Edad</p>
                        <p class="text-gray-800">{{ $mascota->edad_años ?? '?' }} años</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase font-bold">Alergias</p>
                        <p class="text-gray-800">{{ $mascota->alergias ?? 'Ninguna registrada' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase font-bold">Vacunas</p>
                        <p class="text-green-600 font-semibold">✅ Actualizado</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase font-bold">Temperamento Base</p>
                        <p class="text-gray-800">{{ ucfirst($mascota->temperamento) }}</p>
                    </div>
                </div>
            </div>

            <!-- Botón guardar -->
            <button type="submit" class="w-full px-6 py-3 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 transition">
                💾 Guardar Estado de Entrada
            </button>
        </form>
    </div>

    <!-- TAB 2: CHECKLIST -->
    <div id="checklist" class="tab-content bg-white rounded-lg shadow-md p-6 mb-6 hidden">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">✅ Checklist de Tareas</h2>
        <p class="text-gray-600 mb-4">Marca las tareas completadas. Se requiere mínimo 3 ítems.</p>

        <form action="{{ route('groomer.checklist.guardar', $cita->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($checklistBase as $tarea => $completada)
                <div class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-400 transition cursor-pointer"
                     onclick="toggleCheckbox('{{ $tarea }}')">
                    <input type="checkbox" name="checklist[{{ $tarea }}]" id="{{ $tarea }}" value="1"
                           class="w-6 h-6 rounded border-gray-300 focus:ring-green-500 cursor-pointer"
                           {{ $ficha && json_decode($ficha->checklist_json, true)[$tarea] ?? false ? 'checked' : '' }}>
                    <label for="{{ $tarea }}" class="ml-3 text-gray-800 font-semibold cursor-pointer flex-1">
                        @switch($tarea)
                            @case('uñas_cortadas')
                                ✂️ Uñas cortadas
                                @break
                            @case('oidos_limpios')
                                👂 Oídos limpios
                                @break
                            @case('glandulas_anales')
                                💧 Glándulas anales
                                @break
                            @case('baño_completo')
                                🛁 Baño completo
                                @break
                            @case('secado_completo')
                                🔥 Secado completo
                                @break
                            @case('perfume_aplicado')
                                🌸 Perfume aplicado
                                @break
                            @case('inspección_de_piel')
                                🔍 Inspección de piel
                                @break
                            @case('recomendaciones_dadas')
                                💡 Recomendaciones dadas
                                @break
                        @endswitch
                    </label>
                </div>
                @endforeach
            </div>

            @error('checklist')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror

            <div class="bg-blue-50 p-4 rounded-lg mt-4">
                <p class="text-sm text-gray-700">
                    <span class="font-bold">Nota:</span> Marca al menos 3 tareas para poder cerrar el servicio.
                </p>
            </div>

            <button type="submit" class="w-full px-6 py-3 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600 transition">
                ✅ Guardar Checklist
            </button>
        </form>
    </div>

    <!-- TAB 3: FOTOS -->
    <div id="fotos" class="tab-content bg-white rounded-lg shadow-md p-6 mb-6 hidden">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">📷 Galería de Fotos</h2>
        
        <!-- Formulario de carga -->
        <form action="{{ route('groomer.fotos.cargar', $cita->id) }}" method="POST" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="tipo_foto" class="block text-lg font-semibold text-gray-700 mb-2">
                        📸 Tipo de Foto
                    </label>
                    <select name="tipo_foto" id="tipo_foto" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        <option value="">Selecciona el tipo...</option>
                        <option value="antes">📸 Antes (estado inicial)</option>
                        <option value="durante">📹 Durante (en proceso)</option>
                        <option value="despues">✨ Después (resultado final)</option>
                    </select>
                </div>

                <div>
                    <label for="fotos" class="block text-lg font-semibold text-gray-700 mb-2">
                        📤 Selecciona Imágenes
                    </label>
                    <input type="file" name="fotos[]" id="fotos" multiple accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" required>
                    <p class="text-sm text-gray-600 mt-2">Máximo 10 fotos, 5MB cada una</p>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 transition">
                    📤 Subir Fotos
                </button>
            </div>
        </form>

        <!-- Galería de fotos existentes -->
        @if(count($fotos) > 0)
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Fotos Cargadas</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($fotos as $foto)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $foto['path']) }}" alt="Foto {{ $foto['tipo'] }}"
                         class="w-full h-48 object-cover rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 rounded-lg transition flex items-center justify-center">
                        <span class="text-white text-sm font-bold opacity-0 group-hover:opacity-100 transition">
                            {{ ucfirst($foto['tipo']) }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ $foto['fecha'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-yellow-50 p-6 rounded-lg text-center">
            <p class="text-gray-700">📸 No hay fotos cargadas aún</p>
        </div>
        @endif
    </div>

    <!-- TAB 4: RECOMENDACIONES Y CIERRE -->
    <div id="recomendaciones" class="tab-content bg-white rounded-lg shadow-md p-6 mb-6 hidden">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">💡 Recomendaciones para el Dueño</h2>
        
        <form action="{{ route('groomer.ficha.guardar', $cita->id) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="recomendaciones" class="block text-lg font-semibold text-gray-700 mb-2">
                    📝 Recomendaciones Especiales
                </label>
                <textarea name="recomendaciones" id="recomendaciones" rows="5"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                          placeholder="Ej: Cuidados post-servicio, productos a usar, próxima cita recomendada, alimentos a evitar, etc.">{{ $ficha->recomendaciones ?? '' }}</textarea>
            </div>

            <button type="submit" class="w-full px-6 py-3 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600 transition">
                💾 Guardar Recomendaciones
            </button>
        </form>

        <!-- Botón de cierre de servicio -->
        @if($cita->estado === 'confirmada')
        <div class="mt-8 pt-8 border-t border-gray-300">
            <div class="bg-green-50 p-6 rounded-lg mb-4">
                <h3 class="text-lg font-bold text-green-800 mb-2">✅ ¿Listo para cerrar el servicio?</h3>
                <p class="text-gray-700 mb-4">
                    Antes de cerrar, verifica que:
                </p>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>Hayas completado al menos 3 ítems del checklist</li>
                    <li>Hayas cargado fotos antes y después</li>
                    <li>Hayas registrado las observaciones importantes</li>
                    <li>Hayas registrado el uso de insumos</li>
                </ul>
            </div>

            <form action="{{ route('groomer.servicio.cerrar', $cita->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-6 py-4 bg-red-500 text-white font-bold text-lg rounded-lg hover:bg-red-600 transition"
                        onclick="return confirm('¿Estás seguro de cerrar este servicio? Se notificará al cliente.')">
                    🔒 CERRAR SERVICIO
                </button>
            </form>
        </div>
        @elseif($cita->estado === 'finalizado')
        <div class="mt-8 p-6 bg-green-50 rounded-lg border-2 border-green-500">
            <p class="text-lg font-bold text-green-800">✅ Este servicio ya ha sido finalizado</p>
            <p class="text-gray-700 mt-2">El cliente ha sido notificado para el recojo.</p>
        </div>
        @endif
    </div>
</div>

<!-- Script para tabs -->
<script>
function activeTab(tabName) {
    // Ocultar todos los tabs
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.add('hidden'));
    
    // Mostrar el tab seleccionado
    document.getElementById(tabName).classList.remove('hidden');
    
    // Actualizar estado del botón
    const buttons = document.querySelectorAll('.tab-button');
    buttons.forEach(btn => {
        btn.classList.remove('border-b-2', 'border-blue-500');
        btn.classList.add('border-transparent');
    });
    event.target.classList.add('border-b-2', 'border-blue-500');
}

function toggleCheckbox(id) {
    const checkbox = document.getElementById(id);
    checkbox.checked = !checkbox.checked;
}
</script>

@endsection
