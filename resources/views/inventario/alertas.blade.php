<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            🚨 Centro de Alertas de Inventario (Módulo 7.3)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-gray-800 rounded-lg shadow-lg p-6 text-gray-200 border border-gray-700">
                
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-red-400 tracking-wide">Panel de Control de Suministros Críticos</h1>
                    <a href="{{ route('admin.inventario.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase transition shadow">
                        ← Volver al Almacén
                    </a>
                </div>

                <div class="mb-6 bg-red-950/40 border-l-4 border-red-500 p-4 rounded-r-lg text-red-200">
                    <p class="font-bold text-sm sm:text-base">Total de alertas críticas activas: {{ $cantidadAlertas }}</p>
                    <p class="text-xs text-red-400/90 mt-0.5">Recomendación automática: Se sugiere emitir una orden de reabastecimiento o compra inmediata para las existencias agotadas.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <div class="lg:col-span-2 space-y-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700 pb-2">⚠️ Bajo Stock Técnico e Insumos</h3>
                        <div class="overflow-x-auto rounded-lg border border-gray-700">
                            <table class="w-full border-collapse text-xs text-left">
                                <thead class="bg-gray-900 text-gray-300 font-semibold uppercase text-[10px] tracking-wider border-b border-gray-700">
                                    <tr>
                                        <th class="p-3">Nombre / Suministro</th>
                                        <th class="p-3 text-center">Stock Actual</th>
                                        <th class="p-3 text-center">Mínimo</th>
                                        <th class="p-3 text-center">Faltante</th>
                                        <th class="p-3 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700 bg-gray-900/40">
                                    @forelse($insumosBajoStock as $insumo)
                                        <tr class="hover:bg-gray-800/60 transition-colors">
                                            <td class="p-3 font-bold text-white">
                                                🧪 {{ $insumo->nombre }}
                                                <span class="block text-[10px] text-indigo-400 font-semibold mt-0.5 uppercase">{{ $insumo->categoria }}</span>
                                            </td>
                                            <td class="p-3 text-center font-bold text-red-400 font-mono text-sm bg-red-950/10">{{ $insumo->cantidad_disponible }} {{ $insumo->unidad }}</td>
                                            <td class="p-3 text-center text-gray-400 font-mono">{{ $insumo->cantidad_minima }} {{ $insumo->unidad }}</td>
                                            
                                            <td class="p-3 text-center font-bold text-red-500 font-mono text-sm">
                                                -{{ ($insumo->cantidad_minima - $insumo->cantidad_disponible) }} {{ $insumo->unidad }}
                                            </td>
                                            
                                            <td class="p-3 text-center">
                                                <a href="{{ route('admin.inventario.edit', $insumo) }}" class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-[10px] px-2.5 py-1 rounded uppercase tracking-wider transition shadow">
                                                    ✏️ Reponer
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-8 text-center text-gray-500 font-medium italic">
                                                ✅ ¡Excelente! Todos los insumos de grooming y productos operan en niveles estables.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 text-gray-400 dark-pagination">
                            {{ $insumosBajoStock->links() }}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider border-b border-gray-700 pb-2">📈 Alto Consumo</h3>
                        <div class="space-y-3">
                            @forelse($alertasAltoConsumo ?? [] as $alerta)
                                <div class="p-4 bg-amber-950/30 border-l-4 border-amber-500 rounded-r border border-gray-700 flex flex-col gap-1 shadow-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[9px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-400 px-2 py-0.5 rounded border border-amber-500/30 font-mono">Gasto Elevado</span>
                                        <span class="text-xs font-mono font-bold text-amber-400">-{{ $alerta->cantidad_usada }} {{ $alerta->insumo->unidad }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-300 mt-1 leading-relaxed">
                                        Suministro <span class="font-bold text-white">{{ $alerta->insumo->nombre }}</span> fue utilizado en altas proporciones por el Groomer <span class="font-semibold text-indigo-400">{{ $alerta->groomer->name }}</span>.
                                    </p>
                                    <span class="text-[10px] text-gray-500 block mt-1 border-t border-gray-700/50 pt-1">
                                        Servicio: {{ $alerta->cita->servicio->nombre ?? 'Grooming General' }} (Cita #{{ $alerta->cita_id }})
                                    </span>
                                </div>
                            @empty
                                <div class="p-4 bg-gray-900/50 rounded border border-gray-700 text-center text-xs text-gray-500 italic">
                                    🔍 No se registran picos anormales de consumo en las peluquerías esta semana.
                        </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>