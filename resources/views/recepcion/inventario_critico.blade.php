<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">📦 Alertas de Inventario Crítico</h2>
    </x-slot>
    <div class="py-12 bg-gray-900 min-h-screen text-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-md">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs bg-gray-950 text-gray-300 uppercase">
                        <tr>
                            <th class="px-6 py-3">Insumo / Producto</th>
                            <th class="px-6 py-3 text-center">Stock Actual</th>
                            <th class="px-6 py-3 text-center">Stock Mínimo</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($insumosCriticos as $insumo)
                            <tr class="hover:bg-gray-700/30 bg-red-950/10">
                                <td class="px-6 py-4 font-bold text-white">🧪 {{ $insumo->nombre }}</td>
                                <td class="px-6 py-4 text-center text-red-400 font-black">{{ $insumo->cantidad_disponible }}</td>
                                <td class="px-6 py-4 text-center text-gray-400">{{ $insumo->cantidad_minima }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs bg-red-900/50 border border-red-600 text-red-200 animate-pulse">
                                        🚨 Reordenar Ya
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-emerald-400">✅ Todos los insumos y productos se encuentran con stock estable.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>