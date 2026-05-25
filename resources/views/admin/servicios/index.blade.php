<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Catálogo de Servicios y Precios
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900 border border-green-700 text-green-300 rounded-md font-bold text-center">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg border dark:border-gray-700 h-fit">
                    <h3 class="text-lg font-bold text-indigo-400 mb-4 border-b border-gray-700 pb-2">Agregar Nuevo Servicio</h3>
                    
                    <form method="POST" action="{{ route('admin.servicios.store') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Nombre del Servicio *</label>
                            <input type="text" name="nombre" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ej: Corte Higiénico" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Duración (Minutos) *</label>
                                <select name="duracion_base" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="30">30 min (0.5 hrs)</option>
                                    <option value="60">60 min (1.0 hrs)</option>
                                    <option value="90">90 min (1.5 hrs)</option>
                                    <option value="120">120 min (2.0 hrs)</option>
                                    <option value="150">150 min (2.5 hrs)</option>
                                    <option value="180">180 min (3.0 hrs)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400">Precio (Bs.) *</label>
                                <input type="number" name="precio" min="0" step="0.50" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="0.00" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400">Descripción (Opcional)</label>
                            <textarea name="descripcion" rows="3" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="¿Qué incluye este servicio?"></textarea>
                        </div>

                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow transition mt-4">
                            Registrar Servicio
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg border dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-200 mb-4 border-b border-gray-700 pb-2">Servicios Ofrecidos por el Spa</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-300">
                            <thead class="bg-gray-900 text-xs uppercase text-indigo-400 border-b border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Nombre y Descripción</th>
                                    <th class="px-4 py-3 text-center">Duración</th>
                                    <th class="px-4 py-3 text-right">Precio Base</th>
                                    <th class="px-4 py-3 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($servicios as $servicio)
                                    <tr class="hover:bg-gray-750 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="font-bold text-white text-base">{{ $servicio->nombre }}</div>
                                            @if($servicio->descripcion)
                                                <div class="text-xs text-gray-500 mt-1">{{ $servicio->descripcion }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-center font-semibold text-indigo-300">
                                            {{ $servicio->duracion_base }} min
                                        </td>
                                        <td class="px-4 py-4 text-right font-bold text-green-400">
                                            {{ number_format($servicio->precio, 2) }} Bs.
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <form method="POST" action="{{ route('admin.servicios.destroy', $servicio->id) }}" onsubmit="return confirm('¿Seguro que deseas eliminar este servicio?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-md transition">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>