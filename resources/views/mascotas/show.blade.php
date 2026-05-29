<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Perfil Clínico y Estético: {{ $mascota->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg border dark:border-gray-700 grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-gray-900/50 p-4 rounded-lg border border-gray-700 flex flex-col items-center justify-center text-center">
                    <div class="text-5xl mb-2">
                        {{ $mascota->especie == 'Perro' ? '🐶' : ($mascota->especie == 'Gato' ? '🐱' : '🐾') }}
                    </div>
                    <h3 class="text-2xl font-bold text-indigo-400">{{ $mascota->nombre }}</h3>
                    <p class="text-sm text-gray-400 font-medium mt-1">Raza: <span class="text-white">{{ $mascota->raza }}</span></p>
                    <p class="text-xs text-gray-500 mt-2">Nacimiento: {{ \Carbon\Carbon::parse($mascota->fecha_nacimiento)->format('d/m/Y') }}</p>
                </div>

                <div class="space-y-3 p-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-300">Variables de Atención</h4>
                    <div>
                        <span class="text-xs text-gray-400 block">Escala de Tamaño:</span>
                        <span class="inline-flex items-center rounded-md bg-blue-900/50 px-2.5 py-1 text-sm font-medium text-blue-300 ring-1 ring-inset ring-blue-700 mt-1">
                            📊 {{ $mascota->tamano }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Temperamento Evaluado:</span>
                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-sm font-medium mt-1
                            {{ $mascota->comportamiento == 'Agresivo' ? 'bg-red-900/50 text-red-300 ring-1 ring-inset ring-red-700' : 
                               ($mascota->comportamiento == 'Nervioso' ? 'bg-yellow-900/50 text-yellow-300 ring-1 ring-inset ring-yellow-700' : 'bg-emerald-900/50 text-emerald-300 ring-1 ring-inset ring-emerald-700') }}">
                            🎭 {{ $mascota->comportamiento }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3 p-2 border-l border-gray-700 pl-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-400">Alertas Médicas y Clínicas</h4>
                    <div>
                        <span class="text-xs text-gray-400 block">Alergias o Contraindicaciones:</span>
                        <p class="text-sm text-gray-200 mt-1 bg-gray-900 p-2 rounded border border-gray-750 min-h-[50px]">
                            {{ $mascota->alergias ?? 'Ninguna registrada actualmente.' }}
                        </p>
                    </div>
                    <div>
                        @if($mascota->carnet_vacunas)
                            <a href="{{ asset('storage/' . $mascota->carnet_vacunas) }}" target="_blank" class="inline-flex items-center gap-2 text-xs bg-amber-600 hover:bg-amber-500 text-white font-bold py-2 px-4 rounded transition shadow">
                                📂 Abrir Carnet de Vacunas Sincronizado
                            </a>
                        @else
                            <span class="text-xs text-gray-500 italic">⚠️ No se adjuntó documento de vacunas.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg border dark:border-gray-700">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-emerald-400">🕰️ Historial Clínico y Control de Servicios</h3>
                    <p class="text-xs text-gray-400 mt-1">Listado cronológico de atenciones, intervenciones y lavados estéticos recibidos por la mascota.</p>
                </div>

                @if($historialCitas->isEmpty())
                    <div class="text-center py-8 bg-gray-900/30 rounded-lg border border-dashed border-gray-700">
                        <p class="text-gray-400 text-sm">Esta mascota no registra atenciones previas en el centro médico/estético.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-300 border border-gray-700 rounded-lg">
                            <thead class="bg-gray-900 text-xs uppercase text-gray-400 border-b border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Fecha de Atención</th>
                                    <th class="px-4 py-3">Bloque Horario</th>
                                    <th class="px-4 py-3">Servicio Estético/Médico</th>
                                    <th class="px-4 py-3">Profesional (Groomer)</th>
                                    <th class="px-4 py-3 text-center">Estado de Ficha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-800/50">
                                @foreach($historialCitas as $cita)
                                    <tr class="hover:bg-gray-750 transition-colors">
                                        <td class="px-4 py-3 font-bold text-white">
                                            {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-400">
                                            {{ $cita->hora_inicio }} a {{ $cita->hora_fin }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-indigo-300">
                                            {{ $cita->servicio->nombre ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-emerald-400 font-semibold">
                                            {{ $cita->groomer->name ?? 'Sin asignar' }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                                {{ $cita->estado == 'Completada' ? 'bg-green-900 text-green-300' : 
                                                  ($cita->estado == 'Confirmada' ? 'bg-blue-900 text-blue-300' : 
                                                  ($cita->estado == 'Cancelada' ? 'bg-red-900 text-red-300' : 'bg-yellow-900 text-yellow-300')) }}">
                                                {{ $cita->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="flex justify-start">
                <a href="{{ route('mascotas.index') }}" class="text-xs bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition">
                    ⬅ Volver al listado general
                </a>
            </div>

        </div>
    </div>
</x-app-layout>