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
                    
                    <div class="flex items-center gap-2">
                        @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                            <a href="{{ route('admin.cierre_caja') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-md transition shadow text-sm flex items-center gap-1">
                                📊 Cierre de Caja
                            </a>
                        @endif
                        <a href="{{ route('citas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition shadow text-sm">
                            + Nueva Cita
                        </a>
                    </div>
                </div>

                @if($citas->isEmpty())
                    <p class="text-gray-400 text-center py-6">No hay citas registradas en el sistema en este momento.</p>
                @else
                    
                    @if(Auth::user()->rol_id == 4)
                        @php
                            $hoy = \Carbon\Carbon::now('America/La_Paz')->format('Y-m-d');
                            
                            // Próximas: Solo citas agendadas por suceder que no estén terminadas ni canceladas
                            $proximas = $citas->filter(function ($cita) use ($hoy) {
                                return $cita->fecha >= $hoy && in_array($cita->estado, ['Pendiente', 'Confirmada', 'En Progreso']);
                            });

                            // Historial: Citas que ya pasaron de fecha, o que explícitamente se completaron o cancelaron
                            $historial = $citas->filter(function ($cita) use ($hoy) {
                                return $cita->fecha < $hoy || in_array($cita->estado, ['Completada', 'Cancelada']);
                            });
                        @endphp

                        <h4 class="text-md font-bold text-emerald-400 mb-3">📌 Mis Próximas Citas</h4>
                        @if($proximas->isEmpty())
                            <p class="text-gray-500 text-sm mb-6 italic">No tienes citas futuras programadas o activas.</p>
                        @else
                            <div class="overflow-x-auto mb-8">
                                <table class="w-full text-left text-sm text-gray-300 border border-gray-700 rounded-lg">
                                    <thead class="bg-gray-900 text-xs uppercase text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3">Fecha y Hora</th>
                                            <th class="px-4 py-3">Mascota</th>
                                            <th class="px-4 py-3">Servicio</th>
                                            <th class="px-4 py-3 text-center">Estado Servicio</th>
                                            <th class="px-4 py-3 text-center">Estado Pago</th>
                                            <th class="px-4 py-3 text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                                        @foreach($proximas as $cita)
                                            <tr class="hover:bg-gray-750 transition-colors">
                                                <td class="px-4 py-3 font-bold">
                                                    {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }} 
                                                    <span class="text-xs text-indigo-300 font-normal">({{ $cita->hora_inicio }})</span>
                                                </td>
                                                <td class="px-4 py-3">{{ $cita->mascota->nombre ?? 'N/A' }}</td>
                                                <td class="px-4 py-3">{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cita->estado == 'Confirmada' ? 'bg-green-900 text-green-300' : ($cita->estado == 'En Progreso' ? 'bg-blue-900 text-blue-300' : 'bg-yellow-900 text-yellow-300') }}">
                                                        {{ $cita->estado }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($cita->estado_pago === 'Pagado')
                                                        <span class="px-2 py-1 text-xs font-bold rounded-full bg-emerald-950 text-emerald-400 border border-emerald-900">💰 Pagado</span>
                                                    @else
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-amber-950 text-amber-400 border border-amber-900">⏳ Pendiente en Caja</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @if($cita->estado === 'Pendiente' || $cita->estado === 'Confirmada')
                                                        <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" class="inline-flex items-center gap-1 bg-gray-900 p-1 rounded border border-gray-700">
                                                            @csrf
                                                            <input type="hidden" name="aceptar_politica" value="1">
                                                            <select name="motivo_cancelacion" required class="bg-gray-900 border-0 text-white text-xs rounded p-1 cursor-pointer h-7 focus:ring-0">
                                                                <option value="" disabled selected>Motivo...</option>
                                                                <option value="Salud">🏥 Salud</option>
                                                                <option value="Tiempo">⏰ Tiempo</option>
                                                                <option value="Emergencia">🚨 Emergencia</option>
                                                                <option value="Otros">📝 Otros</option>
                                                            </select>
                                                            <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold h-7 px-2 rounded text-xs transition" onclick="return confirm('¿Aceptas las políticas de cancelación? Recuerda que requiere 24 horas de anticipación.')">
                                                                Cancelar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-xs text-gray-500 italic">No cancelable</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <h4 class="text-md font-bold text-gray-400 mb-3 mt-4">🕰️ Historial de Servicios Pasados / Completados</h4>
                        @if($historial->isEmpty())
                            <p class="text-gray-500 text-sm italic">Aún no tienes un historial de servicios cerrados.</p>
                        @else
                            <div class="overflow-x-auto opacity-75">
                                <table class="w-full text-left text-sm text-gray-400 border border-gray-700 rounded-lg">
                                    <thead class="bg-gray-900 text-xs uppercase text-gray-500">
                                        <tr>
                                            <th class="px-4 py-3">Fecha de Atención</th>
                                            <th class="px-4 py-3">Mascota</th>
                                            <th class="px-4 py-3">Servicio Realizado</th>
                                            <th class="px-4 py-3 text-center">Estado Servicio</th>
                                            <th class="px-4 py-3 text-center">Estado Pago / Comprobante</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                                        @foreach($historial as $cita)
                                            <tr>
                                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</td>
                                                <td class="px-4 py-3">{{ $cita->mascota->nombre ?? 'N/A' }}</td>
                                                <td class="px-4 py-3">{{ $cita->servicio->nombre ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $cita->estado === 'Cancelada' ? 'bg-red-950 text-red-400 border border-red-900' : 'bg-indigo-950 text-indigo-300 border border-indigo-900' }}">
                                                        {{ $cita->estado }}
                                                    </span>
                                                </td>
                                                
                                                <td class="px-4 py-3 text-center">
                                                    @if($cita->estado_pago === 'Pagado' || $cita->estado === 'Completada')
                                                        <div class="flex flex-col items-center justify-center gap-1">
                                                            <span class="text-xs text-emerald-400 font-bold">BOB Pagado</span>
                                                            <a href="{{ route('citas.recibo', $cita->id) }}" class="text-[11px] text-blue-400 hover:underline">
                                                                📄 Ver Recibo
                                                            </a>
                                                        </div>
                                                    @else
                                                        <span class="text-xs text-gray-500 italic">No cobrado</span>
                                                    @endif
                                                </td>
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
                                        
                                        @if(Auth::user()->rol_id == 3)
                                            <th class="px-4 py-3 text-center border-l border-gray-700">Operación</th>
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
                                                    {{ $cita->estado == 'Completada' ? 'bg-indigo-900 text-indigo-300' : 
                                                      ($cita->estado == 'Confirmada' ? 'bg-green-900 text-green-300' : 
                                                      ($cita->estado == 'Pendiente' ? 'bg-yellow-900 text-yellow-300' : 'bg-red-900 text-red-300')) }}">
                                                    {{ $cita->estado }}
                                                </span>
                                            </td>
                                            
                                            @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                                                <td class="px-4 py-4 border-l border-gray-700">
                                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                                        
                                                        @if(($cita->estado === 'Confirmada' || $cita->estado === 'Completada') && $cita->estado_pago !== 'Pagado')
                                                            <a href="{{ route('citas.cobrar', $cita->id) }}" class="bg-amber-600 hover:bg-amber-500 text-white font-bold py-1 px-2.5 rounded text-xs transition shadow">
                                                                💵 Cobrar
                                                            </a>
                                                        @endif

                                                        @if($cita->estado_pago === 'Pagado')
                                                            <a href="{{ route('citas.recibo', $cita->id) }}" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-1 px-2.5 rounded text-xs transition shadow">
                                                                📄 Ver Recibo
                                                            </a>
                                                        @endif

                                                        @if($cita->estado === 'Pendiente')
                                                            <form action="{{ route('citas.aprobar', $cita->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-1 px-2.5 rounded text-xs transition shadow">
                                                                    Aprobar
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if($cita->estado === 'Pendiente' || $cita->estado === 'Confirmada')
                                                            <a href="{{ route('citas.calendario') }}" class="text-xs bg-cyan-700 hover:bg-cyan-600 text-white px-2 py-1 rounded font-bold shadow transition-all">
                                                                Reprogramar
                                                            </a>

                                                            <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" class="inline-flex items-center gap-1 bg-gray-900 p-1 rounded border border-gray-700">
                                                                @csrf
                                                                <input type="hidden" name="aceptar_politica" value="1">
                                                                <select name="motivo_cancelacion" required class="bg-gray-900 border-0 text-white text-xs rounded p-1 cursor-pointer h-7 focus:ring-0">
                                                                    <option value="" disabled selected>Motivo...</option>
                                                                    <option value="Salud">🏥 Salud</option>
                                                                    <option value="Tiempo">⏰ Tiempo</option>
                                                                    <option value="Emergencia">🚨 Emergencia</option>
                                                                    <option value="Otros">📝 Otros</option>
                                                                </select>
                                                                <button type="submit" class="bg-red-600 hover:bg-red-500 text-white font-bold h-7 px-2 rounded text-xs transition" onclick="return confirm('¿Seguro que deseas cancelar esta cita?')">
                                                                    Cancelar
                                                                </button>
                                                            </form>
                                                        @endif

                                                        @if($cita->estado === 'Cancelada')
                                                            <span class="text-xs text-red-400 bg-red-950/40 border border-red-900 px-3 py-1 rounded-md italic">
                                                                🚫 Motivo: {{ $cita->motivo_cancelacion ?? 'No especificado' }}
                                                            </span>
                                                        @endif

                                                    </div>
                                                </td>
                                            @endif

                                            @if(Auth::user()->rol_id == 3)
                                                <td class="px-4 py-4 text-center border-l border-gray-700">
                                                    @if($cita->estado == 'Confirmada' || $cita->estado == 'En Progreso')
                                                        <a href="{{ route('groomer.ficha.panel', $cita->id) }}" class="text-xs bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-full font-bold shadow-md transition-all inline-block">
                                                            ✂️ Atender Mascota
                                                        </a>
                                                    @elseif($cita->estado == 'Completada')
                                                        <span class="text-xs bg-gray-700 text-indigo-300 px-3 py-1.5 rounded-full border border-indigo-900 font-bold">✨ Completado</span>
                                                    @else
                                                        <span class="text-xs text-gray-500 italic">No disponible ({{ $cita->estado }})</span>
                                                    @endif
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