<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Auditoría de Sistema (Logs)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border dark:border-gray-700 p-6">
                
                <div class="mb-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Historial de eventos críticos del sistema. Monitoreo de accesos y modificaciones.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-900 dark:text-gray-400 border-b dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold">Fecha y Hora</th>
                                <th scope="col" class="px-6 py-4 font-bold">Acción Realizada</th>
                                <th scope="col" class="px-6 py-4 font-bold">Usuario y Rol</th>
                                <th scope="col" class="px-6 py-4 font-bold">Dirección IP</th>
                                <th scope="col" class="px-6 py-4 font-bold">Navegador</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-indigo-500 font-semibold">
                                        {{ $log['fecha'] }}
                                    </td>
                                    
                                    <td class="px-6 py-4 text-gray-900 dark:text-white font-bold">
                                        {{ ucfirst($log['accion']) }}
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        @php
                                            $id = $log['detalles']['Quien (ID)'] ?? ($log['detalles']['Admin (ID)'] ?? 'N/A');
                                            $rol = $log['detalles']['Rol'] ?? null;
                                        @endphp
                                        
                                        <div class="text-gray-900 dark:text-gray-200 font-bold mb-1">ID: {{ $id }}</div>
                                        
                                        @if($rol == 1)
                                            <span class="px-2 py-1 text-[10px] font-bold bg-red-900/50 text-red-400 border border-red-500 rounded-md">ADMINISTRADOR</span>
                                        @elseif($rol == 2)
                                            <span class="px-2 py-1 text-[10px] font-bold bg-blue-900/50 text-blue-400 border border-blue-500 rounded-md">RECEPCIÓN</span>
                                        @elseif($rol == 3)
                                            <span class="px-2 py-1 text-[10px] font-bold bg-indigo-900/50 text-indigo-400 border border-indigo-500 rounded-md">GROOMER</span>
                                        @elseif($rol == 4)
                                            <span class="px-2 py-1 text-[10px] font-bold bg-gray-700 text-gray-300 border border-gray-500 rounded-md">CLIENTE</span>
                                        @else
                                            <span class="text-xs text-gray-500">-</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-4 font-mono text-sm text-blue-500">
                                        {{ $log['detalles']['Desde donde (IP)'] ?? 'N/A' }}
                                    </td>
                                    
                                    <td class="px-6 py-4 text-xs text-gray-400" title="{{ $log['detalles']['Navegador'] ?? 'N/A' }}">
                                        {{ Str::limit($log['detalles']['Navegador'] ?? 'Desconocido', 40) }}
                                    </td>
                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No hay registros de auditoría todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>