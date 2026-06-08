<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📊 {{ __('Mi Panel de Rendimiento Técnico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- 🧩 BLOQUE 1: TARJETAS DE PRODUCTIVIDAD INDIVIDUAL -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tarjeta de Servicios -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <div class="text-sm font-bold text-gray-400 uppercase tracking-wider">🐾 Servicios Realizados</div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-indigo-400">{{ $totalServicios }}</span>
                        <span class="text-sm text-gray-500">atenciones exitosas</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Suma total de fichas técnicas cerradas durante el periodo[cite: 4].</p>
                </div>

                <!-- Tarjeta de Tiempos -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                    <div class="text-sm font-bold text-gray-400 uppercase tracking-wider">⏱️ Tiempo Promedio Invertido</div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-emerald-400">{{ $tiempoPromedio }}</span>
                        <span class="text-sm text-gray-500">minutos por mascota</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Métrica calculada en base a la duración parametrizada de cada servicio[cite: 4].</p>
                </div>
            </div>

            <!-- 📜 BLOQUE 2: HISTORIAL DE SERVICIOS REALIZADOS -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-gray-200 mb-4">Fichas Técnicas de Estética Cerradas</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-400">
                        <thead class="text-xs bg-gray-900 text-gray-300 uppercase">
                            <tr>
                                <th class="px-6 py-3">Paciente / Mascota</th>
                                <th class="px-6 py-3">Diagnóstico de Ingreso</th>
                                <th class="px-6 py-3">Evidencia Fotográfica</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($fichasCerradas as $ficha)
                                <tr class="hover:bg-gray-700/30">
                                    <td class="px-6 py-4 font-bold text-white">
                                        🐶 {{ $ficha->cita->mascota->nombre ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ $ficha->estado_ingreso ?? 'Sin observaciones anotadas.' }}[cite: 1]
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            @if($ficha->foto_antes_url)
                                                <span class="px-2 py-0.5 text-xs rounded bg-gray-900 border border-gray-600 text-gray-300">Antes</span>
                                            @endif
                                            @if($ficha->foto_despues_url)
                                                <span class="px-2 py-0.5 text-xs rounded bg-emerald-950 border border-emerald-600 text-emerald-400">Después</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No registras fichas de grooming cerradas todavía[cite: 4].</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 📦 BLOQUE 3: CONSUMO PERSONAL DE INSUMOS -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-700">
                <h3 class="text-lg font-bold text-gray-200 mb-4">Materiales y Suministros Utilizados</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-400">
                        <thead class="text-xs bg-gray-900 text-gray-300 uppercase">
                            <tr>
                                <th class="px-6 py-3">Nombre del Material</th>
                                <th class="px-6 py-3 text-center">Entregado</th>
                                <th class="px-6 py-3 text-center">Consumido</th>
                                <th class="px-6 py-3 text-center">Devuelto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($consumoInsumos as $log)
                                <tr class="hover:bg-gray-700/30">
                                    <td class="px-6 py-4 font-semibold text-gray-200">
                                        🧪 {{ $log->insumo->nombre ?? 'Insumo' }}[cite: 5]
                                    </td>
                                    <td class="px-6 py-4 text-center text-indigo-300 font-bold">{{ $log->cantidad_entregada }}</td>
                                    <td class="px-6 py-4 text-center text-emerald-400 font-bold">{{ $log->cantidad_usada }}</td>
                                    <td class="px-6 py-4 text-center text-blue-400 font-bold">{{ $log->cantidad_devuelta }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No se registran mermas ni retiros de insumos a tu nombre hoy[cite: 4].</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>