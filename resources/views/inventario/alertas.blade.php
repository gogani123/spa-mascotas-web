@extends('layouts.app')

@section('title', 'Alertas de Bajo Stock')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-red-600">🔴 Alertas de Bajo Stock</h1>
            <a href="{{ route('inventario.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                ← Volver a Inventario
            </a>
        </div>

        <!-- Resumen -->
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
            <p class="text-red-700 font-bold text-lg">Total de alertas: {{ $cantidadAlertas }}</p>
            <p class="text-red-600 text-sm">Estos insumos necesitan reorden urgente</p>
        </div>

        <!-- Tabla de alertas -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-red-100">
                    <tr>
                        <th class="border p-3 text-left">Nombre</th>
                        <th class="border p-3 text-left">Categoría</th>
                        <th class="border p-3 text-center">Stock Actual</th>
                        <th class="border p-3 text-center">Stock Mínimo</th>
                        <th class="border p-3 text-center">Diferencia</th>
                        <th class="border p-3 text-center">Proveedor</th>
                        <th class="border p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insumosBajoStock as $insumo)
                        <tr class="hover:bg-red-50 transition border-b">
                            <td class="border p-3 font-bold text-red-700">{{ $insumo->nombre }}</td>
                            <td class="border p-3">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $insumo->categoria }}</span>
                            </td>
                            <td class="border p-3 text-center font-bold text-red-600">{{ $insumo->cantidad_disponible }} {{ $insumo->unidad }}</td>
                            <td class="border p-3 text-center">{{ $insumo->cantidad_minima }} {{ $insumo->unidad }}</td>
                            <td class="border p-3 text-center font-bold text-red-600">
                                -{{ ($insumo->cantidad_minima - $insumo->cantidad_disponible) }} {{ $insumo->unidad }}
                            </td>
                            <td class="border p-3 text-center">
                                {{ $insumo->proveedor ?? 'N/A' }}
                            </td>
                            <td class="border p-3 text-center">
                                <a href="{{ route('inventario.edit', $insumo) }}" class="text-blue-600 hover:text-blue-800 text-sm">✏️ Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border p-3 text-center text-gray-500">✅ ¡Excelente! Todos los insumos tienen stock adecuado</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $insumosBajoStock->links() }}
        </div>
    </div>
</div>
@endsection
