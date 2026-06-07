<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📋 {{ __('Mi Agenda de Trabajo') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <p class="text-gray-600 dark:text-gray-400">Servicios asignados para hoy y próximas citas</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6 border dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-400 mb-2">Vista:</label>
                    <div class="flex gap-2">
                        <a href="{{ route('groomer.agenda', ['fecha' => $fecha, 'vista' => 'dia']) }}"
                           class="px-4 py-2 rounded text-xs font-bold uppercase transition {{ $vista === 'dia' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300' }}">
                           📅 Por Día
                        </a>
                        <a href="{{ route('groomer.agenda', ['fecha' => $fecha, 'vista' => 'semana']) }}"
                           class="px-4 py-2 rounded text-xs font-bold uppercase transition {{ $vista === 'semana' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300' }}">
                           📆 Por Semana
                        </a>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-400 mb-2">Fecha:</label>
                    <form method="GET" class="flex gap-2">
                        <input type="date" name="fecha" value="{{ $fecha }}" 
                               class="flex-1 px-3 py-1.5 bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded text-sm text-gray-800 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <input type="hidden" name="vista" value="{{ $vista }}">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded text-xs font-bold uppercase transition shadow">
                            Filtrar
                        </button>
                    </form>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-400 mb-2">Acciones:</label>
                    <a href="{{ route('groomer.agenda', ['fecha' => now()->format('Y-m-d'), 'vista' => 'dia']) }}"
                       class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded text-xs font-bold uppercase transition inline-block shadow">
                        🏠 Hoy
                    </a>
                </div>
            </div>
        </div>

        @if($vista === 'dia')
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-2">📅 {{ \Carbon\Carbon::parse($fecha)->format('l, d de F de Y') }}</h2>
            <p class="text-lg">Total de citas asignadas: <span class="font-black text-emerald-300 font-mono">{{ $citas->count() }}</span></p>
            <p class="text-sm mt-2 text-indigo-200">Duración total estimada en cabina: 
                <span class="font-bold text-white font-mono">{{ $citas->sum(fn($c) => $c->duracion_estimada ?? 60) }} minutos</span>
            </p>
        </div>
        @endif

        <div class="grid grid-cols-1 gap-4">
            @forelse($citas as $cita)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border dark:border-gray-700">
                <div class="border-l-4 {{ $cita->estado === 'confirmada' || $cita->estado === 'Confirmada' ? 'border-indigo-500' : ($cita->estado === 'finalizado' || $cita->estado === 'Completada' ? 'border-emerald-500' : 'border-amber-500') }} p-4">
                    
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-black text-gray-800 dark:text-gray-200">🐾 {{ $cita->mascota->nombre }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $cita->mascota->raza }} • {{ $cita->mascota->tamano ?? $cita->mascota->tamaño }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase
                                {{ $cita->estado === 'confirmada' || $cita->estado === 'Confirmada' ? 'bg-indigo-100 dark:bg-indigo-950 text-indigo-800 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700' : ($cita->estado === 'finalizado' || $cita->estado === 'Completada' ? 'bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700' : 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-700') }}">
                                {{ $cita->estado }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700 font-mono">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Hora</p>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($cita->hora_fin)->format('H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Duración</p>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $cita->duracion_estimada ?? 60 }} min</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Servicio</p>
                            <p class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $cita->servicio->nombre }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Cliente</p>
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $cita->cliente->name }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('groomer.ficha.panel', $cita->id) }}"
                           class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded text-center text-xs font-bold uppercase tracking-wider transition shadow">
                           📄 Abrir / Ver Ficha
                        </a>

                        <a href="{{ route('groomer.insumos.panel', $cita->id) }}"
                           class="flex-1 px-4 py-2 bg-purple-700 hover:bg-purple-600 text-white rounded text-center text-xs font-bold uppercase tracking-wider transition shadow">
                           🛠️ Cargar Insumos
                        </a>
                    </div>

                    @if($cita->fichaGrooming)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <span class="font-bold text-gray-500 font-mono text-xs uppercase tracking-wide">Estado de Ingreso:</span> {{ $cita->fichaGrooming->estado_ingreso ?? 'No registrado' }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-gray-50 dark:bg-gray-900 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-12 text-center">
                <p class="text-xl text-gray-500 dark:text-gray-400 font-bold">😊 No tienes citas agendadas para esta fecha.</p>
                <p class="text-gray-400 dark:text-gray-600 text-sm mt-2">Los cupos aprobados por recepción aparecerán automáticamente aquí.</p>
            </div>
            @endforelse
        </div>

        <div class="bg-gray-100 dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg p-6 mt-8 font-mono text-xs">
            <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-wider">📌 Monitor de Flujo de Estados</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-6 bg-indigo-500 rounded"></div>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-bold text-gray-800 dark:text-white">Confirmada:</span> Aprobada por recepción, lista para iniciar.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-6 bg-amber-500 rounded"></div>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-bold text-gray-800 dark:text-white">En Progreso:</span> Mascota actualmente en cabina de estética.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-3 h-6 bg-emerald-500 rounded"></div>
                    <p class="text-gray-600 dark:text-gray-400"><span class="font-bold text-gray-800 dark:text-white">Completada:</span> Servicio finalizado, inventario descontado.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>