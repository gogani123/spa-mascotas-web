@extends('layouts.app')

@section('title', 'Gestión de Inventario')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">📦 Gestión de Inventario</h1>
            <a href="{{ route('inventario.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                ➕ Nuevo Insumo
            </a>
        </div>

        <!-- Alert de bajo stock -->
        @if($totalBajoStock > 0)
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-yellow-700 font-semibold">⚠️ Tienes {{ $totalBajoStock }} insumo(s) con bajo stock</p>
                <a href="{{ route('inventario.alertas') }}" class="text-yellow-600 underline">Ver alertas</a>
            </div>
        @endif

        <!-- Tabla de insumos -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-3 text-left">Nombre</th>
                        <th class="border p-3 text-left">Categoría</th>
                        <th class="border p-3 text-center">Stock</th>
                        <th class="border p-3 text-center">Mínimo</th>
                        <th class="border p-3 text-center">Estado</th>
                        <th class="border p-3 text-center">Precio Unit.</th>
                        <th class="border p-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insumos as $insumo)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border p-3 font-semibold">{{ $insumo->nombre }}</td>
                            <td class="border p-3">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">{{ $insumo->categoria }}</span>
                            </td>
                            <td class="border p-3 text-center font-bold">{{ $insumo->cantidad_disponible }} {{ $insumo->unidad }}</td>
                            <td class="border p-3 text-center">{{ $insumo->cantidad_minima }} {{ $insumo->unidad }}</td>
                            <td class="border p-3 text-center">
                                @if($insumo->cantidad_disponible <= $insumo->cantidad_minima)
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">🔴 Bajo Stock</span>
                                @elseif($insumo->cantidad_disponible <= ($insumo->cantidad_minima * 1.5))
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">🟡 Advertencia</span>
                                @else
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">✅ Normal</span>
                                @endif
                            </td>
                            <td class="border p-3 text-center">Bs. {{ number_format($insumo->precio_unitario, 2) }}</td>
                            <td class="border p-3 text-center">
                                <a href="{{ route('inventario.edit', $insumo) }}" class="text-blue-600 hover:text-blue-800">✏️ Editar</a>
                                <form method="POST" action="{{ route('inventario.destroy', $insumo) }}" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 ml-2" onclick="return confirm('¿Estás seguro?')">🗑️ Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border p-3 text-center text-gray-500">No hay insumos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-6">
            {{ $insumos->links() }}
        </div>
    </div>
</div>
@endsection
