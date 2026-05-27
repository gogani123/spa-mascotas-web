@extends('layouts.app')

@section('title', 'Crear Nuevo Insumo')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">➕ Crear Nuevo Insumo</h1>

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('inventario.store') }}" class="space-y-6">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block font-semibold text-gray-700 mb-2">Nombre del Insumo *</label>
                <input type="text" name="nombre" id="nombre" class="w-full border rounded-lg px-3 py-2" required value="{{ old('nombre') }}">
            </div>

            <!-- Categoría -->
            <div>
                <label for="categoria" class="block font-semibold text-gray-700 mb-2">Categoría *</label>
                <select name="categoria" id="categoria" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">-- Selecciona una categoría --</option>
                    <option value="Champú" {{ old('categoria') == 'Champú' ? 'selected' : '' }}>Champú</option>
                    <option value="Acondicionador" {{ old('categoria') == 'Acondicionador' ? 'selected' : '' }}>Acondicionador</option>
                    <option value="Herramientas" {{ old('categoria') == 'Herramientas' ? 'selected' : '' }}>Herramientas</option>
                    <option value="Toallas" {{ old('categoria') == 'Toallas' ? 'selected' : '' }}>Toallas</option>
                    <option value="Medicinas" {{ old('categoria') == 'Medicinas' ? 'selected' : '' }}>Medicinas</option>
                    <option value="Accesorios" {{ old('categoria') == 'Accesorios' ? 'selected' : '' }}>Accesorios</option>
                    <option value="Otros" {{ old('categoria') == 'Otros' ? 'selected' : '' }}>Otros</option>
                </select>
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block font-semibold text-gray-700 mb-2">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('descripcion') }}</textarea>
            </div>

            <!-- Stock Disponible -->
            <div>
                <label for="cantidad_disponible" class="block font-semibold text-gray-700 mb-2">Cantidad Disponible *</label>
                <input type="number" name="cantidad_disponible" id="cantidad_disponible" class="w-full border rounded-lg px-3 py-2" min="0" required value="{{ old('cantidad_disponible') }}">
            </div>

            <!-- Stock Mínimo -->
            <div>
                <label for="cantidad_minima" class="block font-semibold text-gray-700 mb-2">Cantidad Mínima (para alertas) *</label>
                <input type="number" name="cantidad_minima" id="cantidad_minima" class="w-full border rounded-lg px-3 py-2" min="1" required value="{{ old('cantidad_minima') }}">
            </div>

            <!-- Unidad -->
            <div>
                <label for="unidad" class="block font-semibold text-gray-700 mb-2">Unidad de Medida *</label>
                <select name="unidad" id="unidad" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">-- Selecciona una unidad --</option>
                    <option value="Unidad" {{ old('unidad') == 'Unidad' ? 'selected' : '' }}>Unidad</option>
                    <option value="Litro" {{ old('unidad') == 'Litro' ? 'selected' : '' }}>Litro</option>
                    <option value="Kilogramo" {{ old('unidad') == 'Kilogramo' ? 'selected' : '' }}>Kilogramo</option>
                    <option value="Metro" {{ old('unidad') == 'Metro' ? 'selected' : '' }}>Metro</option>
                </select>
            </div>

            <!-- Precio Unitario -->
            <div>
                <label for="precio_unitario" class="block font-semibold text-gray-700 mb-2">Precio Unitario (Bs.) *</label>
                <input type="number" name="precio_unitario" id="precio_unitario" class="w-full border rounded-lg px-3 py-2" min="0.01" step="0.01" required value="{{ old('precio_unitario') }}">
            </div>

            <!-- Proveedor -->
            <div>
                <label for="proveedor" class="block font-semibold text-gray-700 mb-2">Proveedor</label>
                <input type="text" name="proveedor" id="proveedor" class="w-full border rounded-lg px-3 py-2" value="{{ old('proveedor') }}">
            </div>

            <!-- Botones -->
            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold">✅ Crear Insumo</button>
                <a href="{{ route('inventario.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold">❌ Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
