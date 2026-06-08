<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">📅 Cronograma Operativo Diario</h2>
    </x-slot>
    <div class="py-12 bg-gray-900 min-h-screen text-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-md">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs bg-gray-950 text-gray-300 uppercase">
                        <tr>
                            <th class="px-6 py-3">Hora</th>
                            <th class="px-6 py-3">Mascota / Dueño</th>
                            <th class="px-6 py-3">Servicio</th>
                            <th class="px-6 py-3">Groomer Asignado</th>
                            <th class="px-6 py-3 text-center">Estado Pago</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($citasHoy as $cita)
                            <tr class="hover:bg-gray-700/30">
                                <td class="px-6 py-4 font-bold text-indigo-400">{{ $cita->hora_inicio }}</td>
                                <td class="px-6 py-4 text-white">🐾 {{ $cita->mascota->nombre }}</td>
                                <td class="px-6 py-4">{{ $cita->servicio->nombre }}</td>
                                <td class="px-6 py-4">👤 {{ $cita->groomer->name ?? 'Por asignar' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs @if($cita->estado_pago == 'Pagado') bg-emerald-950 border border-emerald-600 text-emerald-400 @else bg-amber-950 border border-amber-600 text-amber-400 @endif">
                                        {{ $cita->estado_pago ?? 'Pendiente' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay citas agendadas para el día de hoy.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>