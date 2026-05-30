<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Hoja de Atención Operativa - {{ $cita->mascota->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                
                <div class="mb-6 border-b border-gray-700 pb-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-indigo-400">Ficha en Tiempo Real</h3>
                        <p class="text-xs text-gray-400">Servicio actual: {{ $cita->servicio->nombre }}</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-900 text-indigo-300 rounded-full text-xs font-bold">
                        Groomer: {{ Auth::user()->name }}
                    </span>
                </div>

                <!-- Formulario Principal de Cierre de Servicio -->
                <form method="POST" action="{{ route('citas.completar', $cita->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- SECCIÓN 1: ESTADO INICIAL -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">1. Ficha Técnica: Estado Inicial de la Mascota *</label>
                        <textarea name="estado_inicial" rows="3" class="w-full bg-gray-900 border-gray-700 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500/20 text-sm" placeholder="Ej: El perrito ingresa de buen ánimo, presenta algunos nudos leves en la zona del lomo y piel sana..." required></textarea>
                    </div>

                    <!-- SECCIÓN 2: CHECKLIST -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-3">2. Checklist del Servicio Realizado</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-gray-900 p-4 rounded-md border border-gray-700">
                            <label class="flex items-center space-x-3 text-sm text-gray-300 cursor-pointer">
                                <input type="checkbox" name="checklist[baño]" value="Completado" class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-indigo-500" checked>
                                <span>🧼 Baño e Higiene General</span>
                            </label>
                            <label class="flex items-center space-x-3 text-sm text-gray-300 cursor-pointer">
                                <input type="checkbox" name="checklist[secado]" value="Completado" class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span>💨 Secado y Cepillado de Pelaje</span>
                            </label>
                            <label class="flex items-center space-x-3 text-sm text-gray-300 cursor-pointer">
                                <input type="checkbox" name="checklist[corte]" value="Completado" class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span>✂️ Corte de Pelo (Según raza)</span>
                            </label>
                            <label class="flex items-center space-x-3 text-sm text-gray-300 cursor-pointer">
                                <input type="checkbox" name="checklist[uñas_oidos]" value="Completado" class="rounded bg-gray-800 border-gray-700 text-indigo-600 focus:ring-indigo-500">
                                <span>💅 Corte de Uñas y Limpieza de Oídos</span>
                            </label>
                        </div>
                    </div>

                    <!-- ==================================================================== -->
                    <!-- SECCIÓN 3 REEMPLAZADA: GESTIÓN DE INSUMOS DINÁMICOS (MÓDULO 7)       -->
                    <!-- ==================================================================== -->
                    <div class="space-y-4">
                        <label class="block text-sm font-semibold text-gray-300">3. Registro de Salida e Inventario de Insumos (Módulo 7)</label>
                        
                        <!-- Mini-formulario asíncrono para retirar insumos (7.1) -->
                        <div class="bg-gray-900 p-4 rounded-md border border-gray-700 space-y-3">
                            <p class="text-xs font-bold text-indigo-400 uppercase">📥 Retirar Insumo Autorizado de Almacén (Antes del Servicio)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                                <div>
                                    <label class="block text-[10px] text-gray-400 uppercase mb-1">Seleccionar Insumo</label>
                                    <select id="select_insumo" class="w-full bg-gray-800 border-gray-700 text-white rounded p-1.5 text-xs focus:ring-1 focus:ring-indigo-500">
                                        @foreach($insumosDisponibles as $insumo)
                                            <option value="{{ $insumo->id }}">🔹 {{ $insumo->nombre }} (Stock: {{ $insumo->cantidad_disponible }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] text-gray-400 uppercase mb-1">Cantidad Entregada</label>
                                    <input type="number" id="cantidad_insumo" min="1" placeholder="Cantidad" class="w-full bg-gray-800 border-gray-700 text-white rounded p-1.5 text-xs focus:ring-1 focus:ring-indigo-500">
                                </div>
                                <button type="button" onclick="agregarInsumoFlujo()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-3 rounded text-xs transition uppercase tracking-wider shadow">
                                    Asignar Insumo
                                </button>
                            </div>
                        </div>

                        <!-- Panel / Tabla de control de consumo (7.2) -->
                        <div class="bg-gray-900 p-4 rounded-md border border-gray-700">
                            <p class="text-xs font-bold text-gray-400 mb-2 uppercase">📋 Panel de Confirmación de Uso (Durante o al Finalizar)</p>
                            <div class="overflow-x-auto rounded border border-gray-700">
                                <table class="w-full text-left text-xs text-gray-400">
                                    <thead class="bg-gray-950 text-gray-300 uppercase text-[10px] font-mono border-b border-gray-700">
                                        <tr>
                                            <th class="px-4 py-2.5">Insumo Entregado</th>
                                            <th class="px-4 py-2.5 text-center">Cant. Recibida</th>
                                            <th class="px-4 py-2.5 text-center">Estado del Consumo (7.2)</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla_insumos_cuerpo" class="divide-y divide-gray-700 bg-gray-800/50">
                                        @forelse($insumosEntregados as $salida)
                                            <tr class="hover:bg-gray-800 transition-colors">
                                                <td class="px-4 py-3 text-white font-semibold">🧪 {{ $salida->insumo->nombre }}</td>
                                                <td class="px-4 py-3 text-center text-indigo-400 font-bold text-sm">{{ $salida->cantidad_entregada }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-4">
                                                        <label class="flex items-center gap-1 cursor-pointer">
                                                            <input type="radio" name="insumo_estado[{{ $salida->id }}]" value="usado" {{ $salida->estado === 'usado' || $salida->estado === 'Entregado' ? 'checked' : '' }} class="text-emerald-600 bg-gray-900 border-gray-700 focus:ring-0 w-3.5 h-3.5">
                                                            <span class="text-emerald-400 font-bold">✅ Usado</span>
                                                        </label>
                                                        <label class="flex items-center gap-1 cursor-pointer">
                                                            <input type="radio" name="insumo_estado[{{ $salida->id }}]" value="desperdiaciado" {{ $salida->estado === 'desperdiaciado' ? 'checked' : '' }} class="text-red-600 bg-gray-900 border-gray-700 focus:ring-0 w-3.5 h-3.5">
                                                            <span class="text-red-400 font-bold">🚨 Merma</span>
                                                        </label>
                                                        <label class="flex items-center gap-1 cursor-pointer">
                                                            <input type="radio" name="insumo_estado[{{ $salida->id }}]" value="devuelto" {{ $salida->estado === 'devuelto' ? 'checked' : '' }} class="text-blue-600 bg-gray-900 border-gray-700 focus:ring-0 w-3.5 h-3.5">
                                                            <span class="text-blue-400 font-bold">↩️ Devuelto</span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 italic">No hay insumos registrados para esta sesión técnica.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- ==================================================================== -->

                    <!-- SECCIÓN 4: EVIDENCIA FOTOGRÁFICA -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-3">4. Evidencia Fotográfica (Antes y Después)</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-900 p-4 rounded-md border border-gray-700 text-center">
                                <span class="block text-xs font-bold text-indigo-400 mb-2">📸 Foto del ANTES</span>
                                <input type="file" name="foto_antes" class="text-xs text-gray-400 w-full file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600">
                            </div>
                            <div class="bg-gray-900 p-4 rounded-md border border-gray-700 text-center">
                                <span class="block text-xs font-bold text-emerald-400 mb-2">📸 Foto del DESPUÉS</span>
                                <input type="file" name="foto_despues" class="text-xs text-gray-400 w-full file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-700 file:text-gray-200 hover:file:bg-gray-600">
                            </div>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN PRINCIPALES -->
                    <div class="flex justify-between items-center border-t border-gray-700 pt-6 mt-8">
                        <a href="{{ route('citas.index') }}" class="px-5 py-2 bg-gray-600 hover:bg-gray-500 text-white font-bold rounded-md shadow text-xs transition">
                            Volver a la Agenda
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-md shadow text-xs transition uppercase tracking-wider">
                            🏁 FINALIZAR Y CIERRE DE SERVICIO
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Script de comunicación asíncrona para el Módulo 7.1 -->
    <script>
        function agregarInsumoFlujo() {
            const select = document.getElementById('select_insumo');
            const cantidadInput = document.getElementById('cantidad_insumo');
            const cantidad = cantidadInput.value;

            if (!cantidad || cantidad < 1) {
                alert('⚠️ Introduce una cantidad válida de insumo.');
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('insumo_id', select.value);
            formData.append('cantidad_entregada', cantidad);

            fetch("{{ route('groomer.insumos.registrar', $cita->id) }}", {
                method: 'POST',
                body: formData
            }).then(res => {
                if (res.ok) {
                    window.location.reload(); 
                } else {
                    alert('❌ Error de almacén: Verifica la disponibilidad del stock.');
                }
            });
        }
    </script>
</x-app-layout>