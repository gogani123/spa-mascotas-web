<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📊 {{ __('Auditoría y Control de Insumos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Contenedor Principal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-700 p-6">
                
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-indigo-400">Comparativa: Materiales Entregados vs. Consumidos</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Módulo de auditoría para el control de mermas, desperdicios y devoluciones del personal de Grooming.</p>
                </div>

                <!-- Tabla de Auditoría -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400 border border-gray-700">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-300 border-b border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3">Fecha y Cita</th>
                                <th scope="col" class="px-6 py-3">Estilista (Groomer)</th>
                                <th scope="col" class="px-6 py-3">Insumo / Suministro</th>
                                <th scope="col" class="px-6 py-3 text-center">Cant. Entregada</th>
                                <th scope="col" class="px-6 py-3 text-center">Cant. Usada</th>
                                <th scope="col" class="px-6 py-3 text-center">Devuelto</th>
                                <th scope="col" class="px-6 py-3 text-center">Estado Final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditoriaInsumos as $registro)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        <span class="block text-xs text-indigo-400 font-bold">{{ \Carbon\Carbon::parse($registro->fecha_salida)->format('d/m/Y H:i') }}</span>
                                        <span class="text-xs text-gray-400">Mascota: {{ $registro->cita->mascota->nombre ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $registro->groomer->name ?? 'No asignado' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-300">
                                        {{ $registro->insumo->nombre ?? 'Insumo Eliminado' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-indigo-300 font-semibold">
                                        {{ $registro->cantidad_entregada }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-emerald-400 font-semibold">
                                        {{ $registro->cantidad_usada }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-blue-400 font-semibold">
                                        {{ $registro->cantidad_devuelta }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($registro->estado === 'Usado')
                                            <span class="px-2 py-1 text-xs font-bold rounded-md bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">✅ Totalmente Usado</span>
                                        @elseif($registro->estado === 'Devuelto')
                                            <span class="px-2 py-1 text-xs font-bold rounded-md bg-blue-500/20 text-blue-400 border border-blue-500/30">🔄 Reincorporado al Stock</span>
                                        @elseif($registro->estado === 'Desperdidado' || $registro->estado === 'Desperdiciado')
                                            <span class="px-2 py-1 text-xs font-bold rounded-md bg-red-500/20 text-red-400 border border-red-500/30">⚠️ Merma / Pérdida</span>
                                        <@else
                                            <span class="px-2 py-1 text-xs font-bold rounded-md bg-amber-500/20 text-amber-400 border border-amber-500/30">📦 En Uso (Entregado)</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="bg-white dark:bg-gray-800">
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                        🚫 No se registran transacciones de insumos ni mermas en los servicios hasta el momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>