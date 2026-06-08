<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">🚨 Registro de Cancelaciones y No-Show</h2>
    </x-slot>
    <div class="py-12 bg-gray-900 min-h-screen text-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-md">
                <table class="w-full text-sm text-left text-gray-400">
                    <thead class="text-xs bg-gray-950 text-gray-300 uppercase">
                        <tr>
                            <th class="px-6 py-3">Fecha / Hora</th>
                            <th class="px-6 py-3">Mascota</th>
                            <th class="px-6 py-3">Cliente</th>
                            <th class="px-6 py-3 text-center">Estado Incidencia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($cancelaciones as $item)
                            <tr class="hover:bg-gray-700/30">
                                <td class="px-6 py-4 text-gray-300">{{ $item->fecha }} a las {{ $item->hora }}</td>
                                <td class="px-6 py-4 text-white">🐾 {{ $item->mascota->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $item->user->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs @if($item->estado == 'No-Show') bg-red-950 border border-red-600 text-red-400 @else bg-amber-950 border border-amber-600 text-amber-400 @endif">
                                        {{ $item->estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Felicidades, no se registran abandonos ni cancelaciones en el historial.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>