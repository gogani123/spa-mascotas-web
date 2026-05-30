<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            ✂️ Panel Operativo del Groomer - Ficha Técnica
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold mb-2">🐾 {{ $mascota->nombre }}</h1>
                    <p class="text-lg">{{ $mascota->raza }} • {{ $mascota->tamano }} • {{ $cita->servicio->nombre }}</p>
                    <p class="text-sm mt-2">Cliente: {{ $cita->cliente->name }} | Teléfono: {{ $cita->cliente->telefono ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-4xl font-bold">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m') }}</p>
                    <p class="text-lg">{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</p>
                    <span class="inline-block mt-2 px-4 py-1 bg-white text-indigo-600 rounded-full font-bold text-xs uppercase tracking-wider">
                        ⚡ {{ $cita->estado }}
                    </span>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-900/80 border border-red-700 text-red-200 rounded-lg font-bold text-center text-sm shadow">
                🛑 {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-900/80 border border-emerald-700 text-emerald-200 rounded-lg font-bold text-center text-sm shadow">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-gray-800 rounded-lg shadow-md mb-6 border-b border-gray-700">
            <div class="flex overflow-x-auto">
                <button onclick="activeTab('estado')" class="tab-button px-6 py-4 font-semibold text-indigo-400 border-b-2 border-indigo-500 transition">
                    📋 Ficha de Ingreso
                </button>
                <button onclick="activeTab('checklist')" class="tab-button px-6 py-4 font-semibold text-gray-400 border-b-2 border-transparent hover:text-indigo-400 transition">
                    ✅ Checklist Técnico
                </button>
                <button onclick="activeTab('fotos')" class="tab-button px-6 py-4 font-semibold text-gray-400 border-b-2 border-transparent hover:text-indigo-400 transition">
                    📷 Evidencia Fotos
                </button>
                <button onclick="activeTab('recomendaciones')" class="tab-button px-6 py-4 font-semibold text-gray-400 border-b-2 border-transparent hover:text-indigo-400 transition">
                    💡 Recomendaciones y Cierre
                </button>
            </div>
        </div>

        <div id="estado" class="tab-content bg-gray-800 rounded-lg shadow-md p-6 mb-6 text-gray-200">
            <h2 class="text-xl font-bold text-indigo-400 mb-4">📋 Registro de Condición Física</h2>
            
            <form action="{{ route('groomer.ficha.guardar', $cita->id) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="estado_ingreso" class="block text-sm font-semibold text-gray-300 mb-2">
                        🔍 Estado de Ingreso (Nudos, parásitos, heridas en la piel) *
                    </label>
                    <textarea name="estado_ingreso" id="estado_ingreso" rows="4" required
                              class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-3 text-sm"
                              placeholder="Escriba el diagnóstico inicial del pelaje y piel...">{{ old('estado_ingreso', $ficha->estado_ingreso ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="temperamento" class="block text-sm font-semibold text-gray-300 mb-2">
                            🎭 Temperamento durante el lavado *
                        </label>
                        <select name="temperamento" id="temperamento" required
                                class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm">
                            <option value="tranquilo" {{ old('temperamento', $ficha->temperamento ?? '') == 'tranquilo' ? 'selected' : '' }}>😴 Tranquilo / Cooperativo</option>
                            <option value="nervioso" {{ old('temperamento', $ficha->temperamento ?? '') == 'nervioso' ? 'selected' : '' }}>😰 Nervioso / Asustadizo</option>
                            <option value="agresivo" {{ old('temperamento', $ficha->temperamento ?? '') == 'agresivo' ? 'selected' : '' }}>😠 Agresivo / Reactivo</option>
                            <option value="inquieto" {{ old('temperamento', $ficha->temperamento ?? '') == 'inquieto' ? 'selected' : '' }}>🤪 Inquieto / Hiperactivo</option>
                        </select>
                    </div>

                    <div>
                        <label for="observaciones" class="block text-sm font-semibold text-gray-300 mb-2">
                            📝 Notas Médicas / Alergias Observadas (Opcional)
                        </label>
                        <input type="text" name="observaciones" id="observaciones" value="{{ old('observaciones', $ficha->observaciones ?? '') }}"
                               class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm"
                               placeholder="Ej: Se detectó dermatitis en el lomo.">
                    </div>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-500 transition text-sm uppercase tracking-wider shadow">
                    💾 Guardar Ficha de Ingreso
                </button>
            </form>
        </div>

        <div id="checklist" class="tab-content bg-gray-800 rounded-lg shadow-md p-6 mb-6 hidden text-gray-200">
            <h2 class="text-xl font-bold text-emerald-400 mb-2">✅ Checklist de Tareas Operativas</h2>
            <p class="text-xs text-gray-400 mb-6">Marca los procesos completados en la mesa de peluquería. (Requerido: Mínimo 3 tareas).</p>

            <form action="{{ route('groomer.checklist.guardar', $cita->id) }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($checklistBase as $tarea => $completada)
                        @php
                            $jsonHistorial = $ficha && $ficha->checklist_json ? json_decode($ficha->checklist_json, true) : [];
                            $estaMarcada = in_array($tarea, $jsonHistorial);
                        @endphp
                        <label class="flex items-center p-4 bg-gray-900/60 border border-gray-700 rounded-lg hover:border-emerald-500 transition cursor-pointer">
                            <input type="checkbox" name="checklist[]" value="{{ $tarea }}" 
                                   class="w-5 h-5 rounded border-gray-700 bg-gray-800 text-emerald-500 focus:ring-indigo-500 cursor-pointer"
                                   {{ $estaMarcada ? 'checked' : '' }}>
                            <span class="ml-3 font-semibold text-sm capitalize">
                                {{ str_replace('_', ' ', $tarea) }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-500 transition text-sm uppercase tracking-wider shadow">
                    ✔️ Guardar Avance del Checklist
                </button>
            </form>
        </div>

        <div id="fotos" class="tab-content bg-gray-800 rounded-lg shadow-md p-6 mb-6 hidden text-gray-200">
            <h2 class="text-xl font-bold text-indigo-400 mb-4">📷 Galería de Evidencia Visual</h2>
            
            <form action="{{ route('groomer.fotos.cargar', $cita->id) }}" method="POST" enctype="multipart/form-data" class="mb-8 bg-gray-900/40 p-4 rounded-lg border border-gray-700 space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tipo_foto" class="block text-xs font-bold text-gray-400 uppercase">Progreso del Servicio</label>
                        <select name="tipo_foto" id="tipo_foto" required class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md p-2.5 text-sm">
                            <option value="antes">📸 Foto del Antes (Ingreso)</option>
                            <option value="durante">📹 Foto del Durante (Proceso)</option>
                            <option value="despues">✨ Foto del Después (Finalizado)</option>
                        </select>
                    </div>
                    <div>
                        <label for="input_fotos" class="block text-xs font-bold text-gray-400 uppercase">Subir Archivos de Imagen</label>
                        <input type="file" name="fotos[]" id="input_fotos" multiple accept="image/*" required
                               class="w-full mt-1 text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer bg-gray-900 border border-gray-700 rounded-md p-1">
                    </div>
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded text-xs uppercase tracking-wider transition shadow">
                    📤 Subir Bloque de Fotos
                </button>
            </form>

            @if(count($fotos) > 0)
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3">Historial Fotográfico Cargado:</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($fotos as $foto)
                            <div class="bg-gray-900 p-2 rounded-lg border border-gray-700">
                                <img src="{{ asset('storage/' . $foto['path']) }}" alt="Foto" class="w-full h-32 object-cover rounded shadow-inner">
                                <div class="flex justify-between items-center mt-2 px-1 text-[10px]">
                                    <span class="px-2 py-0.5 rounded-full font-bold uppercase {{ $foto['tipo'] == 'despues' ? 'bg-green-900 text-green-300' : 'bg-gray-700 text-gray-300' }}">
                                        {{ $foto['tipo'] }}
                                    </span>
                                    <span class="text-gray-500">{{ \Carbon\Carbon::parse($foto['fecha'])->format('d/m H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-gray-900/50 p-6 rounded-lg text-center border border-dashed border-gray-700">
                    <p class="text-gray-400 text-sm">📸 No se han adjuntado fotos de evidencia para esta cita.</p>
                </div>
            @endif
        </div>

        <div id="recomendaciones" class="tab-content bg-gray-800 rounded-lg shadow-md p-6 mb-6 hidden text-gray-200">
            <h2 class="text-xl font-bold text-indigo-400 mb-4">💡 Indicaciones de Entrega</h2>
            
            @if($cita->estado === 'En Progreso')
                <div class="bg-gray-900/60 p-6 rounded-lg border border-gray-700">
                    <h3 class="text-lg font-bold text-amber-400 mb-2">⚠️ Control de Cierre del Servicio</h3>
                    <p class="text-sm text-gray-400 mb-4 leading-relaxed">
                        Al confirmar el cierre, el sistema guardará la sesión técnica, liberará tu disponibilidad en la agenda del spa y **enviará una alerta automática al correo electrónico del cliente** con el mensaje de "Listo para Recoger".
                    </p>
                    
                    <form action="{{ route('groomer.servicio.cerrar', $cita->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-6 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-base rounded-lg transition transform hover:scale-[1.01] shadow shadow-emerald-900/50"
                                onclick="return confirm('¿Confirmas que completaste el checklist mínimo y deseas finalizar el servicio de la mascota?')">
                            🔒 CERRAR FICHA Y NOTIFICAR RECOJO
                        </button>
                    </form>
                </div>
            @else
                <div class="p-6 bg-emerald-900/30 rounded-lg border-2 border-emerald-600 text-center">
                    <p class="text-lg font-bold text-emerald-400">✅ Ficha Clínica de Atención Completada</p>
                    <p class="text-sm text-gray-400 mt-2">El servicio concluyó exitosamente. La mascota se encuentra lista en el mostrador de recepción.</p>
                    <a href="{{ route('citas.index') }}" class="inline-block mt-4 text-xs bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded font-bold transition">
                        ⬅ Volver al listado de citas
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
    function activeTab(tabName) {
        // Ocultar todos los contenidos de las pestañas
        const tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(tab => tab.classList.add('hidden'));
        
        // Quitar estados activos de todos los botones
        const buttons = document.querySelectorAll('.tab-button');
        buttons.forEach(btn => {
            btn.classList.remove('text-indigo-400', 'border-indigo-500');
            btn.classList.add('text-gray-400', 'border-transparent');
        });

        // Activar la pestaña elegida por ID
        document.getElementById(tabName).classList.remove('hidden');
        
        // Marcar el botón presionado como activo
        event.currentTarget.classList.remove('text-gray-400', 'border-transparent');
        event.currentTarget.classList.add('text-indigo-400', 'border-indigo-500');
    }
    </script>
</x-app-layout>