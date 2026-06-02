<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Modificar Parámetros de Insumo
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-gray-800 rounded-lg shadow-lg p-6 text-gray-200 border border-gray-700">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-indigo-400">Editar Insumo</h1>
                    <p class="text-xs text-gray-400 mt-1">Modificando: <span class="text-white font-semibold">{{ $insumo->nombre }}</span></p>
                </div>
                <a href="{{ route('admin.inventario.index') }}" class="text-xs bg-gray-700 hover:bg-gray-600 text-white py-2 px-4 rounded font-bold transition shadow-sm">
                    Volver al Almacén
                </a>
            </div>

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-900/80 border border-red-700 text-red-200 rounded-lg font-bold text-sm shadow">
                    🛑 {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.inventario.update', $insumo) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="nombre" class="block font-semibold text-sm text-gray-300 mb-2">Nombre del Insumo *</label>
                    <input type="text" name="nombre" id="nombre" 
                           class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm font-semibold" 
                           required value="{{ old('nombre', $insumo->nombre) }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="categoria" class="block font-semibold text-sm text-gray-300 mb-2">Categoría *</label>
                        <select name="categoria" id="categoria" required 
                                class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm">
                            <option value="Champú" {{ old('categoria', $insumo->categoria) == 'Champú' ? 'selected' : '' }}>Champú</option>
                            <option value="Acondicionador" {{ old('categoria', $insumo->categoria) == 'Acondicionador' ? 'selected' : '' }}>Acondicionador</option>
                            <option value="Herramientas" {{ old('categoria', $insumo->categoria) == 'Herramientas' ? 'selected' : '' }}>Herramientas</option>
                            <option value="Toallas" {{ old('categoria', $insumo->categoria) == 'Toallas' ? 'selected' : '' }}>Toallas</option>
                            <option value="Medicinas" {{ old('categoria', $insumo->categoria) == 'Medicinas' ? 'selected' : '' }}>Medicinas</option>
                            <option value="Accesorios" {{ old('categoria', $insumo->categoria) == 'Accesorios' ? 'selected' : '' }}>Accesorios</option>
                            <option value="Otros" {{ old('categoria', $insumo->categoria) == 'Otros' ? 'selected' : '' }}>Otros</option>
                        </select>
                    </div>

                    <div>
                        <label for="unidad" class="block font-semibold text-sm text-gray-300 mb-2">Unidad de Medida *</label>
                        <select name="unidad" id="unidad" required 
                                class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm">
                            <option value="Unidad" {{ old('unidad', $insumo->unidad) == 'Unidad' ? 'selected' : '' }}>Unidad</option>
                            <option value="Litro" {{ old('unidad', $insumo->unidad) == 'Litro' ? 'selected' : '' }}>Litro</option>
                            <option value="Kilogramo" {{ old('unidad', $insumo->unidad) == 'Kilogramo' ? 'selected' : '' }}>Kilogramo</option>
                            <option value="Metro" {{ old('unidad', $insumo->unidad) == 'Metro' ? 'selected' : '' }}>Metro</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="cantidad_disponible" class="block font-semibold text-sm text-gray-300 mb-2">Stock Disponible *</label>
                        <input type="number" name="cantidad_disponible" id="cantidad_disponible" min="0" required
                               class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm font-mono font-bold" 
                               value="{{ old('cantidad_disponible', $insumo->cantidad_disponible) }}">
                    </div>

                    <div>
                        <label for="cantidad_minima" class="block font-semibold text-sm text-gray-300 mb-2">Stock Mínimo Alerta *</label>
                        <input type="number" name="cantidad_minima" id="cantidad_minima" min="1" required
                               class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm font-mono text-gray-400" 
                               value="{{ old('cantidad_minima', $insumo->cantidad_minima) }}">
                    </div>

                    <div>
                        <label for="precio_unitario" class="block font-semibold text-sm text-gray-300 mb-2">Precio Unitario (Bs.) *</label>
                        <input type="number" name="precio_unitario" id="precio_unitario" step="0.01" min="0.01" required
                               class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm font-mono text-emerald-400 font-bold" 
                               value="{{ old('precio_unitario', $insumo->precio_unitario) }}">
                    </div>
                </div>

                <div>
                    <label for="proveedor" class="block font-semibold text-sm text-gray-300 mb-2">Proveedor Registrado</label>
                    <input type="text" name="proveedor" id="proveedor" 
                           class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm" 
                           value="{{ old('proveedor', $insumo->proveedor) }}">
                </div>

                <div>
                    <label for="descripcion" class="block font-semibold text-sm text-gray-300 mb-2">Descripción Técnica</label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                              class="w-full bg-gray-900 border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 p-2.5 text-sm"
                              placeholder="Detalles sobre el insumo...">{{ old('descripcion', $insumo->descripcion) }}</textarea>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-all text-sm uppercase tracking-wider shadow-md hover:shadow-indigo-500/20 transform active:scale-[0.99]">
                    Actualizar Información de Insumo
                </button>
            </form>
        </div>
    </div>
</x-app-layout>