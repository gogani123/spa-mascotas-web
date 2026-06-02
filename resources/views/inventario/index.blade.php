<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Gestión de Inventario e Insumos
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-gray-800 rounded-lg shadow-lg p-6 text-gray-200 border border-gray-700">
            
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-3xl font-bold text-indigo-400 tracking-wide">Almacén Central de Insumos</h1>
                <a href="{{ route('admin.inventario.create') }}" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-4 py-2.5 rounded-lg text-sm shadow transition flex items-center justify-center gap-1">
                    ➕ Nuevo Insumo
                </a>
            </div>

            @if($totalBajoStock > 0)
                <div class="mb-6 bg-yellow-950/40 border-l-4 border-yellow-500 p-4 rounded-r-lg text-yellow-200 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-sm sm:text-base">⚠️ Alerta de Suministros</p>
                        <p class="text-xs text-yellow-400/90 mt-0.5">Tienes {{ $totalBajoStock }} insumo(s) operando en niveles mínimos o críticos.</p>
                    </div>
                    <a href="{{ route('admin.inventario.alertas') }}" class="text-xs sm:text-sm bg-yellow-600 hover:bg-yellow-500 text-gray-900 font-bold px-3 py-1.5 rounded transition shadow">
                        Ver Alertas
                    </a>
                </div>
            @endif

            <div class="overflow-x-auto rounded-lg border border-gray-700">
                <table class="w-full border-collapse text-sm text-left">
                    <thead class="bg-gray-900 text-gray-300 font-semibold uppercase tracking-wider text-xs border-b border-gray-700">
                        <tr>
                            <th class="p-4">Nombre de Insumo</th>
                            <th class="p-4">Categoría</th>
                            <th class="p-4 text-center">Stock Actual</th>
                            <th class="p-4 text-center">Mínimo Permitido</th>
                            <th class="p-4 text-center">Estado</th>
                            <th class="p-4 text-center">Precio Unit.</th>
                            <th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-900/40">
                        @forelse($insumos as $insumo)
                            <tr class="hover:bg-gray-800/60 transition-colors">
                                <td class="p-4 font-bold text-white">{{ $insumo->nombre }}</td>
                                <td class="p-4">
                                    <span class="bg-indigo-950 text-indigo-300 border border-indigo-800/60 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                        {{ $insumo->categoria }}
                                    </span>
                                </td>
                                <td class="p-4 text-center font-bold text-gray-100 font-mono">
                                    {{ $insumo->cantidad_disponible }} <span class="text-xs text-gray-400 font-normal">{{ $insumo->unidad }}</span>
                                </td>
                                <td class="p-4 text-center text-gray-400 font-mono">
                                    {{ $insumo->cantidad_minima }} <span class="text-xs text-gray-500 font-normal">{{ $insumo->unidad }}</span>
                                </td>
                                <td class="p-4 text-center">
                                    @if($insumo->cantidad_disponible <= $insumo->cantidad_minima)
                                        <span class="inline-block bg-red-950 text-red-400 border border-red-800/50 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">
                                            Crítico
                                        </span>
                                    @elseif($insumo->cantidad_disponible <= ($insumo->cantidad_minima * 1.5))
                                        <span class="inline-block bg-amber-950 text-amber-400 border border-amber-800/50 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">
                                            Advertencia
                                        </span>
                                    @else
                                        <span class="inline-block bg-emerald-950 text-emerald-400 border border-emerald-800/50 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">
                                            Estable
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-center font-semibold font-mono text-gray-200">
                                    Bs. {{ number_format($insumo->precio_unitario, 2) }}
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('admin.inventario.edit', $insumo) }}" class="text-blue-400 hover:text-blue-300 font-semibold transition text-xs">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.inventario.destroy', $insumo) }}" class="inline">
                                            @csrf
                                            
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 font-semibold transition text-xs" 
                                                    onclick="return confirm('¿Estás completamente seguro de que deseas eliminar este insumo del almacén?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-gray-500 font-medium">
                                    📦 No se han registrado insumos en el inventario global de la veterinaria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 dark-pagination text-gray-400">
                {{ $insumos->links() }}
            </div>
        </div>
    </div>
</x-app-layout>