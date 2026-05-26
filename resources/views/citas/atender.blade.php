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

                <form method="POST" action="{{ route('citas.completar', $cita->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">1. Ficha Técnica: Estado Inicial de la Mascota *</label>
                        <textarea name="estado_inicial" rows="3" class="w-full bg-gray-900 border-gray-700 text-gray-200 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500/20 text-sm" placeholder="Ej: El perrito ingresa de buen ánimo, presenta algunos nudos leves en la zona del lomo y piel sana..." required></textarea>
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
                        <label class="block text-sm font-semibold text-gray-300 mb-3">3. Registro e Inventario de Insumos</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-gray-900 p-4 rounded-md border border-gray-700">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Champú Usado (ml)</label>
                                <input type="text" name="insumo_shampoo" class="w-full bg-gray-800 border-gray-700 text-white rounded p-1.5 text-xs" value="50ml">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Perfume / Colonia</label>
                                <select name="insumo_perfume" class="w-full bg-gray-800 border-gray-700 text-white rounded p-1.5 text-xs">
                                    <option value="Fragancia Bebé">Fragancia Bebé</option>
                                    <option value="Fragancia Cítrica">Fragancia Cítrica</option>
                                    <option value="No usado">No usado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Algodón / Paños</label>
                                <input type="text" name="insumo_algodon" class="w-full bg-gray-800 border-gray-700 text-white rounded p-1.5 text-xs" value="2 motas">
                            </div>
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

            </div>
        </div>
    </div>
</x-app-layout>