<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Gestión de Bloqueos de Agenda (Excepciones)
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
                    <h3 class="text-lg font-bold text-indigo-400 mb-4">Bloquear una Fecha</h3>
                    
                    <form method="POST" action="{{ route('admin.bloqueos.store') }}" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-400">Fecha del Bloqueo *</label>
                            <input type="date" name="fecha" min="{{ date('Y-m-d') }}" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" style="color-scheme: dark;" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-400">Motivo / Razón *</label>
                            <select name="motivo" class="w-full mt-1 bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="Feriado nacional / local">Feriado nacional o local</option>
                                <option value="Mantenimiento de infraestructura">Mantenimiento general del Spa</option>
                                <option value="Ausencia de personal / Capacitación">Ausencia técnica / Capacitación</option>
                                <option value="Emergencia sanitaria / climática">Emergencia inesperada</option>
                            </select>
                        </div>

                        <div class="flex items-center pt-2">
                            <input type="checkbox" id="todo_el_dia" name="todo_el_dia" value="1" checked class="w-4 h-4 text-indigo-600 bg-gray-900 border-gray-700 rounded focus:ring-indigo-500">
                            <label for="todo_el_dia" class="ms-2 text-sm font-medium text-gray-300">Bloquear todo el día de atención</label>
                        </div>

                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow transition">
                            Aplicar Restricción de Agenda
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg border dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-200 mb-4">Fechas Bloqueadas Activas</h3>
                    
                    @if($bloqueos->isEmpty())
                        <p class="text-gray-400 text-sm text-center py-8">No hay fechas bloqueadas actualmente. La agenda sigue las reglas generales del Spa.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-300">
                                <thead class="bg-gray-900 text-xs uppercase text-indigo-400 border-b border-gray-700">
                                    <tr>
                                        <th class="px-4 py-3">Fecha</th>
                                        <th class="px-4 py-3">Motivo</th>
                                        <th class="px-4 py-3 text-center">Rango</th>
                                        <th class="px-4 py-3 text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700">
                                    @foreach($bloqueos as $bloqueo)
                                        <tr class="hover:bg-gray-750 transition-colors">
                                            <td class="px-4 py-4 font-bold text-white">
                                                {{ \Carbon\Carbon::parse($bloqueo->fecha)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-4 text-gray-400">
                                                {{ $bloqueo->motivo }}
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-900 text-red-300">
                                                    Todo el día
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <form method="POST" action="{{ route('admin.bloqueos.destroy', $bloqueo->id) }}" onsubmit="return confirm('¿Seguro que deseas levantar este bloqueo y permitir citas en esta fecha?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm bg-red-600 hover:bg-red-700 text-white font-bold px-3 py-1.5 rounded-md transition">
                                                        Levantar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>