<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                Panel de Gestión de Citas (Administración)
            @elseif(Auth::user()->rol_id == 3)
                Mi Agenda de Trabajo (Groomer)
            @else
                Mis Citas Agendadas
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900 border border-green-700 text-green-300 rounded-md font-bold text-center">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-indigo-400">Listado General</h3>
                    
                    <a href="{{ route('citas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition shadow text-sm">
                        + Nueva Cita
                    </a>
                </div>

                @if($citas->isEmpty())
                    <p class="text-gray-400 text-center py-6">No hay citas registradas en el sistema bajo tu usuario en este momento.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-300">
                            <thead class="bg-gray-900 text-xs uppercase text-gray-400 border-b border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Fecha y Horario</th>
                                    <th class="px-4 py-3">Cliente y Mascota</th>
                                    <th class="px-4 py-3">Servicio</th>
                                    <th class="px-4 py-3">Groomer</th>
                                    <th class="px-4 py-3 text-center">Estado</th>
                                    
                                    @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                                        <th class="px-4 py-3 text-center border-l border-gray-700">Acciones / Cobranza</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($citas as $cita)
                                    <tr class="hover:bg-gray-750 transition-colors">
                                        <td class="px-4 py-4 font-bold text-white">
                                            {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }} <br>
                                            <span class="text-xs text-indigo-300 font-normal">{{ $cita->hora_inicio }} a {{ $cita->hora_fin }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-white">{{ $cita->cliente->name ?? 'Usuario Eliminado' }}</span> <br>
                                            <span class="text-gray-400 text-xs font-bold">Mascota: {{ $cita->mascota->nombre ?? 'N/A' }}</span>
                                        </td>
                                        <td class="px-4 py-4">{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                                        <td class="px-4 py-4 text-emerald-400 font-semibold">{{ $cita->groomer->name ?? 'Por asignar' }}</td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $cita->estado == 'Confirmada' ? 'bg-green-900 text-green-300' : 
                                                ($cita->estado == 'Cancelada' ? 'bg-red-900 text-red-300' : 'bg-yellow-900 text-yellow-300') }}">
                                                {{ $cita->estado }}
                                            </span>
                                        </td>
                                        
                                        @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                                            <td class="px-4 py-4 border-l border-gray-700">
                                                <div class="flex items-center justify-center gap-3">
                                                    
                                                    <a href="{{ route('citas.calendario') }}" class="text-xs bg-blue-600 hover:bg-blue-500 text-white px-3.5 py-1.5 rounded-full font-medium shadow-md transition-all duration-150 transform hover:-translate-y-0.5">
                                                        Reprogramar
                                                    </a>

                                                    @if($cita->estado_pago == 'Pendiente')
                                                        <a href="{{ route('citas.cobrar', $cita->id) }}" class="text-xs bg-emerald-600 hover:bg-emerald-500 text-white px-3.5 py-1.5 rounded-full font-medium shadow-md transition-all duration-150 transform hover:-translate-y-0.5">
                                                            Cobrar Bs. {{ number_format($cita->servicio->precio, 2) }}
                                                        </a>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-900/50 px-3 py-1.5 text-xs font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-700 shadow-sm">
                                                            <svg class="h-3.5 w-3.5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                            Pagado ({{ $cita->metodo_pago }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>