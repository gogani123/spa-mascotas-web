<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                Panel de Gestión de Citas (Administración)
            @elseif(Auth::user()->rol_id == 3)
                Mi Agenda de Trabajo (Groomer)
            @else
                Mis Citas y Autogestión
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900 border border-green-700 text-green-300 rounded-md font-bold text-center shadow-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-900 border border-red-700 text-red-300 rounded-md font-bold text-center shadow-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
                    <h3 class="text-lg font-bold text-indigo-400">
                        {{ Auth::user()->rol_id == 4 ? 'Mi Agenda e Historial' : 'Listado General de Citas' }}
                    </h3>
                    
                    <a href="{{ route('citas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition shadow text-sm">
                        + Nueva Cita
                    </a>
                </div>

                @if($citas->isEmpty())
                    <p class="text-gray-400 text-center py-6">No hay citas registradas en el sistema en este momento.</p>
                @else
                    
                    @if(Auth::user()->rol_id == 4)
                        @php
                            $hoy = \Carbon\Carbon::now('America/La_Paz')->format('Y-m-d');
                            $proximas = $citas->where('fecha', '>=', $hoy);
                            $historial = $citas->where('fecha', '<', $hoy);
                        @endphp

                        <h4 class="text-md font-bold text-emerald-400 mb-3">📌 Mis Próximas Citas</h4>
                        @if($proximas->isEmpty())
                            <p class="text-gray-500 text-sm mb-6 italic">No tienes citas futuras programadas.</p>
                        @else
                            <div class="overflow-x-auto mb-8">
                                <table class="w-full text-left text-sm text-gray-300 border border-gray-700 rounded-lg">
                                    <thead class="bg-gray-900 text-xs uppercase text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3">Fecha y Hora</th>
                                            <th class="px-4 py-3">Mascota</th>
                                            <th class="px-4 py-3">Servicio</th>
                                            <th class="px-4 py-3 text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                                        @foreach($proximas as $cita)
                                            <tr class="hover:bg-gray-750 transition-colors">
                                                <td class="px-4 py-3 font-bold">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }} <span class="text-xs text-indigo-300 font-normal">({{ $cita->hora_inicio }})</span></td>
                                                <td class="px-4 py-3">{{ $cita->mascota->nombre ?? 'N/A' }}</td>
                                                <td class="px-4 py-3">{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cita->estado == 'Confirmada' ? 'bg-green-900 text-green-300' : 'bg-yellow-900 text-yellow-300' }}">
                                                        {{ $cita->estado }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <h4 class="text-md font-bold text-gray-400 mb-3 mt-4">🕰️ Historial de Servicios Pasados</h4>
                        @if($historial->isEmpty())
                            <p class="text-gray-500 text-sm italic">Aún no hay un historial de servicios completados.</p>
                        @else
                            <div class="overflow-x-auto opacity-75">
                                <table class="w-full text-left text-sm text-gray-400 border border-gray-700 rounded-lg">
                                    <thead class="bg-gray-900 text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="px-4 py-3">Fecha de Atención</th>
                                            <th class="px-4 py-3">Mascota</th>
                                            <th class="px-4 py-3">Servicio Realizado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                                        @foreach($historial as $cita)
                                            <tr>
                                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                                                <td class="px-4 py-3">{{ $cita->mascota->nombre ?? 'N/A' }}</td>
                                                <td class="px-4 py-3">{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

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
                                                      ($cita->estado == 'Pendiente' ? 'bg-yellow-900 text-yellow-300' : 'bg-red-900 text-red-300') }}">
                                                    {{ $cita->estado }}
                                                </span>
                                            </td>
                                            
                                            @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                                                <td class="px-4 py-4 border-l border-gray-700">
                                                    <div class="flex items-center justify-center gap-3">
                                                        
                                                        @if($cita->estado == 'Pendiente')
                                                            <form action="{{ route('citas.aprobar', $cita->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="text-xs bg-yellow-600 hover:bg-yellow-500 text-white px-3.5 py-1.5 rounded-full font-medium shadow-md transition-all duration-150 transform hover:-translate-y-0.5">
                                                                    Aprobar
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if($cita->estado == 'Confirmada')
                                                            @php
                                                                $mensaje = urlencode("🐾 ¡Hola " . ($cita->cliente->name ?? '') . "! Te escribimos del Spa para confirmar la cita de *" . ($cita->mascota->nombre ?? '') . "* para el día " . \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') . " a las " . $cita->hora_inicio . " hrs. Servicio: " . ($cita->servicio->nombre ?? '') . ". ¡Te esperamos!");
                                                                $whatsappUrl = "https://wa.me/59170000000?text={$mensaje}";
                                                            @endphp
                                                            <a href="{{ $whatsappUrl }}" target="_blank" class="text-xs bg-green-500 hover:bg-green-400 text-white px-3.5 py-1.5 rounded-full font-medium shadow-md transition-all duration-150 flex items-center gap-1">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                                                Notificar
                                                            </a>
                                                        @endif

                                                        <a href="{{ route('citas.calendario') }}" class="text-xs bg-blue-600 hover:bg-blue-500 text-white px-3.5 py-1.5 rounded-full font-medium shadow-md transition-all duration-150 transform hover:-translate-y-0.5">
                                                            Reprogramar
                                                        </a>

                                                        @if($cita->estado_pago == 'Pendiente')
                                                            <a href="{{ route('citas.cobrar', $cita->id) }}" class="text-xs bg-emerald-600 hover:bg-emerald-500 text-white px-3.5 py-1.5 rounded-full font-medium shadow-md transition-all duration-150 transform hover:-translate-y-0.5">
                                                                Cobrar
                                                            </a>
                                                        @else
                                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-900/50 px-3 py-1.5 text-xs font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-700 shadow-sm">
                                                                Pagado
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
                    @endif
            </div>
        </div>
    </div>
</x-app-layout>