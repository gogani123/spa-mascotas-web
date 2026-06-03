<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Hoja de Atención Operativa - {{ $cita->mascota->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(auth()->user()->rol_id == 1 || auth()->user()->rol_id == 2)
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-indigo-500/30 text-gray-200 mb-6">
                    <h3 class="text-lg font-bold text-indigo-400 mb-2">📦 Autorizar y Despachar Insumos al Groomer</h3>
                    <p class="text-xs text-gray-400 mb-4">Selecciona los materiales que le vas a entregar en mano al estilista para iniciar este servicio.</p>
                    
                    <form action="{{ route('salidas.entregar') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="cita_id" value="{{ $cita->id }}">
                        <input type="hidden" name="groomer_id" value="{{ $cita->groomer_id }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1">Insumo Disponible</label>
                                <select name="insumo_id" required class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2 text-xs focus:ring-1 focus:ring-indigo-500">
                                    <option value="">-- Elegir Material --</option>
                                    @foreach(\App\Models\Insumo::where('cantidad_disponible', '>', 0)->get() as $insumo)
                                        <option value="{{ $insumo->id }}">
                                            {{ $insumo->nombre }} (Disp: {{ $insumo->cantidad_disponible }} {{ $insumo->unidad }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-300 mb-1">Cantidad a Entregar</label>
                                <input type="number" name="cantidad_entregada" min="1" required 
                                       class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2 text-xs font-mono focus:ring-1 focus:ring-indigo-500" 
                                       placeholder="Ej. 1">
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2 px-4 rounded-lg shadow transition">
                                    🚀 Despachar Material
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-300 mb-1">Notas u Observaciones (Opcional)</label>
                            <input type="text" name="observaciones" 
                                   class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2 text-xs focus:ring-1 focus:ring-indigo-500" 
                                   placeholder="Ej. Entregada toalla extra para secado profundo">
                        </div>
                    </form>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 bg-emerald-900/80 border border-emerald-700 text-emerald-200 rounded-lg font-bold text-center text-sm shadow">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-900/80 border border-red-700 text-red-200 rounded-lg font-bold text-center text-sm shadow">
                    🛑 {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                
                <div class="mb-6 border-b border-gray-700 pb-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-indigo-400">Ficha en Tiempo Real</h3>
                        <p class="text-xs text-gray-400">Servicio actual: {{ $cita->servicio->nombre }}</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-900 text-indigo-300 rounded-full text-xs font-bold">
                        Groomer: {{ $cita->groomer->name ?? Auth::user()->name }}
                    </span>
                </div>

                <form method="POST" action="{{ route('citas.completar', $cita->id) }}" enctype="multipart/form-data" class="space-y-6 mb-8">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">1. Ficha Técnica: Estado Inicial de la Mascota *</label>
                        <textarea name="estado_inicial" rows="3" class="w-full bg-gray-900 border-gray-700 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500/20 text-sm" placeholder="Ej: El perrito ingresa de buen ánimo, presenta algunos nudos leves..." required></textarea>
                    </div>

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

                    <div class="flex justify-between items-center border-t border-gray-700 pt-6 mt-8">
                        <a href="{{ route('citas.index') }}" class="px-5 py-2 bg-gray-600 hover:bg-gray-500 text-white font-bold rounded-md shadow text-xs transition">
                            Volver a la Agenda
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-md shadow text-xs transition uppercase tracking-wider">
                            🏁 FINALIZAR Y CIERRE DE SERVICIO
                        </button>
                    </div>
                </form>

                <div class="space-y-4 border-t border-gray-700 pt-6">
                    <label class="block text-sm font-semibold text-gray-300">3. Materiales e Insumos Entregados para el Servicio</label>

                    <div class="bg-gray-900 p-4 rounded-md border border-gray-700">
                        <p class="text-xs font-bold text-gray-400 mb-2 uppercase">📋 Declaración de Consumo Final</p>
                        <div class="overflow-x-auto rounded border border-gray-700">
                            <table class="w-full text-left text-xs text-gray-400">
                                <thead class="bg-gray-950 text-gray-300 uppercase text-[10px] font-mono border-b border-gray-700">
                                    <tr>
                                        <th class="px-4 py-2.5">Insumo</th>
                                        <th class="px-4 py-2.5 text-center">Cant. Recibida</th>
                                        <th class="px-4 py-2.5 text-center">Cantidad Usada</th>
                                        <th class="px-4 py-2.5 text-center">Cantidad Devuelta</th>
                                        <th class="px-4 py-2.5 text-center">Estado Final</th>
                                        <th class="px-4 py-2.5 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                                    @forelse($cita->salidaInsumos ?? [] as $salida)
                                        <tr class="hover:bg-gray-800 transition-colors">
                                            
                                            @if($salida->estado == 'Entregado')
                                                <td class="px-4 py-3 text-white font-semibold">
                                                    🧪 {{ $salida->insumo->nombre }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-indigo-400 font-bold text-sm">
                                                    {{ $salida->cantidad_entregada }} {{ $salida->insumo->unidad }}
                                                </td>
                                                
                                                <form method="POST" action="{{ route('salidas.actualizarUso', $salida->id) }}" class="inline">
                                                    @csrf
                                                    <td class="px-4 py-3 text-center">
                                                        <input type="number" name="cantidad_usada" value="0" min="0" max="{{ $salida->cantidad_entregada }}" class="w-16 bg-gray-900 border-gray-700 text-white text-center rounded p-1 text-xs font-mono" required>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <input type="number" name="cantidad_devuelta" value="{{ $salida->cantidad_entregada }}" min="0" max="{{ $salida->cantidad_entregada }}" class="w-16 bg-gray-900 border-gray-700 text-white text-center rounded p-1 text-xs font-mono" required>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <select name="estado" class="bg-gray-900 border-gray-700 text-white rounded p-1 text-xs focus:ring-1 focus:ring-indigo-500" required>
                                                            <option value="Devuelto">Devuelto</option>
                                                            <option value="Usado">Usado / Normal</option>
                                                            <option value="Desperdiaciado">Desperdiciado / Merma</option>
                                                        </select>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold px-3 py-1 rounded shadow transition">
                                                            Confirmar
                                                        </button>
                                                    </td>
                                                </form>
                                                
                                            @else
                                                <td class="px-4 py-3 text-gray-500 font-semibold line-through">
                                                    🧪 {{ $salida->insumo->nombre }}
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-500 font-bold text-sm">
                                                    {{ $salida->getFaltantesAttribute() }} {{ $salida->insumo->unidad }}
                                                </td>
                                                <td colspan="3" class="px-4 py-3 text-center">
                                                    <div class="flex flex-col items-center justify-center gap-1">
                                                        <span class="inline-block bg-emerald-950 text-emerald-400 border border-emerald-800/50 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                                            📊 REPORTADO ({{ $salida->estado }})
                                                        </span>
                                                        <div class="text-[11px] text-gray-400 font-medium mt-0.5">
                                                            Usó: <span class="text-white font-mono font-bold">{{ $salida->cantidad_usada }}</span> | 
                                                            Devolvió: <span class="text-indigo-400 font-mono font-bold">{{ $salida->cantidad_devuelta }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center text-gray-500 font-medium italic">
                                                    🔒 Bloqueado
                                                </td>
                                            @endif

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-500 italic">
                                                No se han asignado materiales a este servicio aún. Despáchalos desde el panel de Inventario.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
</x-app-layout>