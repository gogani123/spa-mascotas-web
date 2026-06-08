<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📊 {{ __('Analítica de Satisfacción y NPS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-900 border border-gray-700 p-6 rounded-xl shadow-md">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Calificación Promedio</p>
                    <div class="flex items-center gap-3 mt-2">
                        <p class="text-4xl font-black text-yellow-400">{{ $promedioEstrellas }}</p>
                        <span class="text-2xl text-yellow-400">⭐⭐⭐⭐⭐</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Basado en {{ $totalRespuestas }} evaluaciones en total.</p>
                </div>

                <div class="bg-gray-900 border border-gray-700 p-6 rounded-xl shadow-md">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Índice NPS Neto</p>
                    <p class="text-4xl font-black text-indigo-400 mt-2">{{ $scoreNPS }}</p>
                    <div class="w-full bg-gray-700 h-2 rounded-full mt-3 overflow-hidden">
                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ (($scoreNPS + 100) / 200) * 100 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Escala internacional estándar de -100 a +100.</p>
                </div>

                <div class="bg-gray-900 border border-gray-700 p-6 rounded-xl shadow-md">
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Nivel de Lealtad</p>
                    <p class="text-2xl font-bold mt-2 @if($scoreNPS > 50) text-emerald-400 @elseif($scoreNPS >= 0) text-amber-400 @else text-red-400 @endif">
                        @if($scoreNPS > 50) 🚀 Excelente Servicio
                        @elseif($scoreNPS >= 0) 🟡 Zona Aceptable
                        @else 🚨 Zona de Alerta Interna
                        @endif
                    </p>
                    <p class="text-xs text-gray-500 mt-3">Métrica directa de recomendación post-servicio.</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 border border-gray-700 p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Historial de Comentarios Abiertos</h3>
                <div class="space-y-4">
                    @forelse($encuestas as $item)
                        <div class="border-l-4 @if($item->estrellas >= 4) border-emerald-500 @else border-amber-500 @endif bg-gray-900/40 p-4 rounded-r-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-yellow-400 font-bold">
                                        {{ str_repeat('★', $item->estrellas) }}{{ str_repeat('☆', 5 - $item->estrellas) }}
                                    </span>
                                    <span class="ml-2 text-xs bg-gray-800 text-gray-400 px-2 py-0.5 rounded">NPS: {{ $item->nps }}</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-300 italic mt-2">"{{ $item->comentario ?? 'El cliente no dejó comentarios escritos.' }}"</p>
                            <p class="text-xs text-indigo-400 mt-1 font-semibold">Mascota atendida: {{ $item->cita->mascota->nombre ?? 'N/A' }}</p>
                        </div>
                    @empty
                        <p class="text-center text-sm text-gray-400 py-6">🚫 Aún no se registran encuestas respondidas en el sistema.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>