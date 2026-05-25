<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Mis Mascotas') }}
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
                    <h3 class="text-lg font-bold text-indigo-400">Lista de mis Mascotas</h3>
                    <a href="{{ route('mascotas.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md transition shadow text-sm">
                        + Nueva Mascota
                    </a>
                </div>

                @if($mascotas->isEmpty())
                    <p class="text-gray-400 text-center py-6">Aún no tienes mascotas registradas en el sistema.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-300">
                            <thead class="bg-gray-900 text-xs uppercase text-gray-400 border-b border-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Nombre</th>
                                    <th class="px-4 py-3">Especie y Raza</th>
                                    <th class="px-4 py-3 text-center">Edad</th>
                                    <th class="px-4 py-3 text-center">Peso</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($mascotas as $mascota)
                                    <tr class="hover:bg-gray-750 transition-colors">
                                        <td class="px-4 py-4 font-bold text-white text-base">{{ $mascota->nombre }}</td>
                                        <td class="px-4 py-4">{{ $mascota->especie }} - {{ $mascota->raza }}</td>
                                        <td class="px-4 py-4 text-center">{{ $mascota->edad }} años</td>
                                        <td class="px-4 py-4 text-center">{{ $mascota->peso }} kg</td>
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