@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-gray-800">📋 Mi Agenda</h1>
        <p class="text-gray-600">Servicios asignados para hoy y próximas citas</p>
    </div>

    <!-- Controles de vista y filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Selector de vista -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Vista:</label>
                <div class="flex gap-2">
                    <a href="{{ route('groomer.agenda', ['fecha' => $fecha, 'vista' => 'dia']) }}"
                       class="px-4 py-2 rounded {{ $vista === 'dia' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        📅 Por Día
                    </a>
                    <a href="{{ route('groomer.agenda', ['fecha' => $fecha, 'vista' => 'semana']) }}"
                       class="px-4 py-2 rounded {{ $vista === 'semana' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        📆 Por Semana
                    </a>
                </div>
            </div>

            <!-- Selector de fecha -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Fecha:</label>
                <form method="GET" class="flex gap-2">
                    <input type="date" name="fecha" value="{{ $fecha }}" 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="hidden" name="vista" value="{{ $vista }}">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Filtrar
                    </button>
                </form>
            </div>

            <!-- Botón de regreso a hoy -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Acciones:</label>
                <a href="{{ route('groomer.agenda', ['fecha' => now()->format('Y-m-d'), 'vista' => 'dia']) }}"
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 inline-block">
                    🏠 Hoy
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen del día -->
    @if($vista === 'dia')
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold mb-2">📅 {{ \Carbon\Carbon::parse($fecha)->format('l, d de F de Y') }}</h2>
        <p class="text-lg">Total de citas: <span class="font-bold">{{ $citas->count() }}</span></p>
        <p class="text-sm mt-2">Duración total estimada: 
            <span class="font-bold">{{ $citas->sum(fn($c) => $c->duracion_estimada ?? 60) }} minutos</span>
        </p>
    </div>
    @endif

    <!-- Listado de citas -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($citas as $cita)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="border-l-4 {{ $cita->estado === 'confirmada' ? 'border-blue-500' : ($cita->estado === 'finalizado' ? 'border-green-500' : 'border-yellow-500') }} p-4">
                
                <!-- Encabezado de cita -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">🐾 {{ $cita->mascota->nombre }}</h3>
                        <p class="text-sm text-gray-600">{{ $cita->mascota->raza }} • {{ $cita->mascota->tamaño }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold 
                            {{ $cita->estado === 'confirmada' ? 'bg-blue-100 text-blue-800' : ($cita->estado === 'finalizado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($cita->estado) }}
                        </span>
                    </div>
                </div>

                <!-- Detalles de cita -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4 pb-4 border-b border-gray-200">
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase">Hora</p>
                        <p class="text-lg font-bold text-gray-800">{{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase">Duración</p>
                        <p class="text-lg font-bold text-gray-800">{{ $cita->duracion_estimada ?? 60 }} min</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase">Servicio</p>
                        <p class="text-lg font-bold text-gray-800">{{ $cita->servicio->nombre }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase">Cliente</p>
                        <p class="text-lg font-bold text-gray-800">{{ $cita->cliente->name }}</p>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-2">
                    <!-- Ver Ficha Técnica -->
                    <a href="{{ route('groomer.ficha.panel', $cita->id) }}"
                       class="flex-1 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 text-center font-semibold transition">
                        📄 Ver Ficha
                    </a>

                    <!-- Ver Insumos -->
                    <a href="{{ route('groomer.insumos.panel', $cita->id) }}"
                       class="flex-1 px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 text-center font-semibold transition">
                        🛠️ Insumos
                    </a>

                    <!-- Atender (si está confirmada) -->
                    @if($cita->estado === 'confirmada')
                    <form action="{{ route('groomer.ficha.panel', $cita->id) }}" method="GET" class="flex-1">
                        <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 font-semibold transition">
                            🚀 Comenzar Atención
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Información adicional -->
                @if($cita->fichaGrooming)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Temperamento:</span> {{ ucfirst($cita->fichaGrooming->temperamento ?? 'No registrado') }}
                    </p>
                    @if($cita->fichaGrooming->checklist_json)
                    <p class="text-sm text-gray-700 mt-1">
                        <span class="font-semibold">✅ Checklist: Completado</span>
                    </p>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-yellow-50 border-2 border-yellow-200 rounded-lg p-8 text-center">
            <p class="text-xl text-gray-700 font-semibold">😊 No tienes citas asignadas para esta fecha</p>
            <p class="text-gray-600 mt-2">Vuelve pronto para ver tus próximos servicios</p>
        </div>
        @endforelse
    </div>

    <!-- Leyenda de estados -->
    <div class="bg-gray-50 rounded-lg shadow-md p-6 mt-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4">📋 Leyenda de Estados</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-4 h-8 bg-blue-500 rounded"></div>
                <p class="text-gray-700"><span class="font-bold">Confirmada</span> - Cita agendada y lista para atender</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-4 h-8 bg-yellow-500 rounded"></div>
                <p class="text-gray-700"><span class="font-bold">En proceso</span> - Atención en curso</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-4 h-8 bg-green-500 rounded"></div>
                <p class="text-gray-700"><span class="font-bold">Finalizada</span> - Servicio completado</p>
            </div>
        </div>
    </div>
</div>
@endsection
