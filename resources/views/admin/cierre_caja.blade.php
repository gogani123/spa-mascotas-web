<x-app-layout>
    <div class="py-12 bg-gray-900 min-h-screen text-white">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('citas.index') }}" class="text-gray-400 hover:text-white transition font-bold text-sm flex items-center gap-1">
                    ⬅ Volver al listado de citas
                </a>
            </div>

            <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-2 flex items-center gap-2">
                    <span>🏦</span> Consolidación Diario y Cierre de Caja
                </h2>
                
                <p class="text-gray-400 text-sm mb-6">Fecha de cuadre: <span class="text-indigo-400 font-bold">{{ $hoy }}</span></p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-gray-900 border border-gray-700 p-5 rounded-xl">
                        <p class="text-gray-400 text-xs font-semibold uppercase">Total Recaudado Hoy</p>
                        <p class="text-3xl font-black text-emerald-400 mt-1">BOB {{ number_format($totalCitas, 2) }}</p>
                    </div>
                    <div class="bg-gray-900 border border-gray-700 p-5 rounded-xl">
                        <p class="text-gray-400 text-xs font-semibold uppercase">Transacciones Procesadas</p>
                        <p class="text-3xl font-black text-indigo-400 mt-1">{{ $totalTransacciones }} servicios</p>
                    </div>
                </div>

                <h3 class="text-md font-bold mb-3 text-gray-300">Desglose Consolidado por Métodos de Pago</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm text-gray-400">
                        <thead class="bg-gray-800 text-xs text-gray-300 uppercase font-mono border-b border-gray-700">
                            <tr>
                                <th class="px-6 py-3">Método de Pago</th>
                                <th class="px-6 py-3 text-center">Cantidad de Citas</th>
                                <th class="px-6 py-3 text-right">Subtotal Recaudado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse($consolidadoMetodos as $metodo)
                                {{-- Forzamos a objeto por si viene como array --}}
                                @php $metodo = (object) $metodo; @endphp
                                <tr class="hover:bg-gray-800/30">
                                    <td class="px-6 py-4 font-semibold text-white">💼 {{ $metodo->metodo_pago ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-center">{{ $metodo->cantidad ?? 0 }}</td>
                                    <td class="px-6 py-4 text-right text-emerald-400 font-bold">BOB {{ number_format($metodo->total_metodo ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No se registraron cobros el día de hoy.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>