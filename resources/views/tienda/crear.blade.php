<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            ➕ Registrar Nuevo Producto en Tienda (Panel Administrativo)
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-gray-800 p-8 rounded-lg shadow-xl border border-gray-700 text-gray-200">
                <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                    <p class="text-xs text-gray-400 font-mono uppercase tracking-wider">Formulario de Carga de Suministros</p>
                    <a href="{{ route('tienda.index') }}" class="text-xs bg-gray-700 hover:bg-gray-600 text-white px-3 py-1.5 rounded transition">
                        ⬅ Volver al Catálogo
                    </a>
                </div>

                <!-- FORMULARIO CON SOPORTE PARA SUBIDA DE ARCHIVOS (IMÁGENES) -->
                <form method="POST" action="{{ route('admin.productos.guardar') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- Nombre del Producto -->
                    <div>
                        <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Nombre del Producto *</label>
                        <input type="text" name="nombre" placeholder="Ej: Croquetas Premium Adulto" class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-indigo-500" required>
                    </div>

                    <!-- Fila de Categoría y Variante -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Categoría Oficial *</label>
                            <select name="categoria" class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-indigo-500" required>
                                <option value="">-- Seleccione una --</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Variante (Tamaño, Peso, Marca) *</label>
                            <input type="text" name="variante" placeholder="Ej: Bolsa de 3kg / Fragancia Lavanda" class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-indigo-500" required>
                        </div>
                    </div>

                    <!-- Fila de Control de Stock e Inventario Mínimo -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Precio Unitario (Bs.) *</label>
                            <input type="number" step="0.01" name="precio" placeholder="0.00" class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Stock Inicial *</label>
                            <input type="number" name="stock" placeholder="Cantidad en tienda" class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-indigo-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Stock Mínimo (Alerta) *</label>
                            <input type="number" name="stock_minimo" placeholder="Límite crítico" class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-indigo-500" required>
                        </div>
                    </div>

                    <!-- Descripción Corta -->
                    <div>
                        <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">Descripción del Artículo</label>
                        <textarea name="descripcion" rows="3" placeholder="Detalles o especificaciones del producto para el cliente..." class="w-full bg-gray-900 border-gray-700 text-white rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <!-- Carga de Imagen Fotográfica -->
                    <div class="bg-gray-900 p-4 rounded-lg border border-gray-700">
                        <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1">📷 Fotografía Referencial (Opcional)</label>
                        <p class="text-[11px] text-gray-400 mb-2">Formatos permitidos: JPG, PNG o JPEG. Máximo 2MB.</p>
                        <input type="file" name="imagen" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 file:cursor-pointer">
                    </div>

                    <!-- Botón de Envío -->
                    <div class="pt-4">
                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg shadow-lg text-xs uppercase tracking-widest transition">
                            💾 Guardar e Incorporar al Catálogo
                        </button>
                    </div>
                </form>

            </div>
            
        </div>
    </div>
</x-app-layout>