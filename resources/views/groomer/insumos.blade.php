@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold mb-2">🛠️ Gestión de Insumos</h1>
                <p class="text-lg">{{ $cita->mascota->nombre }} | {{ $cita->servicio->nombre }}</p>
                <p class="text-sm mt-2">{{ $cita->cliente->name }} | {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }} - {{ $cita->hora_inicio }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm">Estado: <span class="px-3 py-1 rounded-full bg-white text-purple-600 font-bold">{{ ucfirst($cita->estado) }}</span></p>
            </div>
        </div>
    </div>

    <!-- Información sobre insumos asignados -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">📦 Insumos Entregados</h2>
        
        @if($insumos->count() > 0)
        <div class="space-y-4">
            @foreach($insumos as $salida)
            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-purple-400 transition">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $salida->insumo->nombre }}</h3>
                        <p class="text-sm text-gray-600">Categoría: {{ $salida->insumo->categoria }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold 
                        {{ $salida->estado === 'usado' ? 'bg-green-100 text-green-800' : 
                           ($salida->estado === 'devuelto' ? 'bg-blue-100 text-blue-800' : 
                           ($salida->estado === 'desperdiciado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($salida->estado) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-200">
                    <div>
                        <p class="text-xs text-gray-600 font-bold uppercase">Cantidad Entregada</p>
                        <p class="text-lg font-bold text-gray-800">{{ $salida->cantidad_entregada }} {{ $salida->insumo->unidad }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-bold uppercase">Usada</p>
                        <p class="text-lg font-bold text-gray-800">{{ $salida->cantidad_usada ?? '0' }} {{ $salida->insumo->unidad }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-bold uppercase">Devuelta</p>
                        <p class="text-lg font-bold text-gray-800">{{ $salida->cantidad_devuelta ?? '0' }} {{ $salida->insumo->unidad }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-bold uppercase">Precio</p>
                        <p class="text-lg font-bold text-gray-800">${{ $salida->insumo->precio_unitario }}</p>
                    </div>
                </div>

                <!-- Formulario de registro de uso -->
                <form action="{{ route('groomer.insumos.usar', $cita->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="insumo_id" value="{{ $salida->insumo->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <!-- Cantidad usada -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                ✅ Cantidad Usada
                            </label>
                            <input type="number" name="cantidad_usada" step="0.1" 
                                   value="{{ $salida->cantidad_usada ?? 0 }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>

                        <!-- Cantidad devuelta -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                🔄 Cantidad Devuelta
                            </label>
                            <input type="number" name="cantidad_devuelta" step="0.1" 
                                   value="{{ $salida->cantidad_devuelta ?? 0 }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="estado_{{ $salida->id }}" class="block text-sm font-semibold text-gray-700 mb-1">
                                📊 Estado Final
                            </label>
                            <select name="estado" id="estado_{{ $salida->id }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="entregado" {{ ($salida->estado ?? 'entregado') === 'entregado' ? 'selected' : '' }}>
                                    Entregado
                                </option>
                                <option value="usado" {{ ($salida->estado ?? '') === 'usado' ? 'selected' : '' }}>
                                    ✅ Usado
                                </option>
                                <option value="devuelto" {{ ($salida->estado ?? '') === 'devuelto' ? 'selected' : '' }}>
                                    🔄 Devuelto
                                </option>
                                <option value="desperdiciado" {{ ($salida->estado ?? '') === 'desperdiciado' ? 'selected' : '' }}>
                                    ❌ Desperdiciado
                                </option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white font-semibold rounded-lg hover:bg-purple-600 transition">
                        💾 Guardar Uso
                    </button>
                </form>
            </div>
            @endforeach
        </div>

        <!-- Resumen de insumos -->
        <div class="mt-6 pt-6 border-t border-gray-300">
            <h3 class="text-lg font-bold text-gray-800 mb-3">📊 Resumen de Uso</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-xs text-blue-600 font-bold uppercase">Total Entregado</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $insumos->sum('cantidad_entregada') }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-xs text-green-600 font-bold uppercase">Total Usado</p>
                    <p class="text-2xl font-bold text-green-800">{{ $insumos->sum('cantidad_usada') ?? 0 }}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg">
                    <p class="text-xs text-orange-600 font-bold uppercase">Total Devuelto</p>
                    <p class="text-2xl font-bold text-orange-800">{{ $insumos->sum('cantidad_devuelta') ?? 0 }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-xs text-red-600 font-bold uppercase">Total Desperdiciado</p>
                    <p class="text-2xl font-bold text-red-800">
                        {{ $insumos->where('estado', 'desperdiciado')->count() }}
                    </p>
                </div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 p-6 rounded-lg text-center border-2 border-yellow-200">
            <p class="text-gray-700 font-semibold">📦 No hay insumos asignados para esta cita</p>
            <p class="text-gray-600 text-sm mt-2">Contacta con recepción si necesitas materiales adicionales</p>
        </div>
        @endif
    </div>

    <!-- Guía de insumos -->
    <div class="bg-gray-50 rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">ℹ️ Guía de Estados de Insumos</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex gap-3">
                <div class="text-2xl">✅</div>
                <div>
                    <p class="font-bold text-gray-800">Usado</p>
                    <p class="text-sm text-gray-600">Se consumió durante el servicio y se descontará del inventario</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="text-2xl">🔄</div>
                <div>
                    <p class="font-bold text-gray-800">Devuelto</p>
                    <p class="text-sm text-gray-600">Se devuelve sin usar y vuelve al inventario</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="text-2xl">❌</div>
                <div>
                    <p class="font-bold text-gray-800">Desperdiciado</p>
                    <p class="text-sm text-gray-600">Se perdió o dañó durante el servicio</p>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="text-2xl">📦</div>
                <div>
                    <p class="font-bold text-gray-800">Entregado</p>
                    <p class="text-sm text-gray-600">Aún no se ha procesado su uso</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de navegación -->
    <div class="flex gap-4">
        <a href="{{ route('groomer.ficha.panel', $cita->id) }}"
           class="flex-1 px-6 py-3 bg-blue-500 text-white font-bold rounded-lg hover:bg-blue-600 transition text-center">
            ← Volver a Ficha
        </a>
        <a href="{{ route('groomer.agenda') }}"
           class="flex-1 px-6 py-3 bg-gray-500 text-white font-bold rounded-lg hover:bg-gray-600 transition text-center">
            📋 Mi Agenda
        </a>
    </div>
</div>
@endsection
