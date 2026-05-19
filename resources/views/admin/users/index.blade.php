<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Personal y Clientes
            </h2>
            <a href="{{ route('admin.users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg transition">
                + Crear Empleado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg border dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-900 dark:text-gray-400 border-b dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-bold">Nombre del Usuario</th>
                                <th scope="col" class="px-6 py-4 font-bold">Contacto</th>
                                <th scope="col" class="px-6 py-4 font-bold">Rol</th>
                                <th scope="col" class="px-6 py-4 font-bold">Datos Laborales</th>
                                <th scope="col" class="px-6 py-4 font-bold">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                        <div class="font-bold">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">CI: {{ $user->ci ?? 'No registrado' }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-gray-900 dark:text-gray-200">{{ $user->email }}</div>
                                        <div class="text-xs text-indigo-500 font-semibold">{{ $user->telefono ?? 'Sin teléfono' }}</div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full 
                                            {{ $user->rol_id == 1 ? 'bg-red-900/50 text-red-400 border border-red-500' : '' }}
                                            {{ $user->rol_id == 2 ? 'bg-blue-900/50 text-blue-400 border border-blue-500' : '' }}
                                            {{ $user->rol_id == 3 ? 'bg-indigo-900/50 text-indigo-400 border border-indigo-500' : '' }}
                                            {{ $user->rol_id == 4 ? 'bg-gray-700 text-gray-300 border border-gray-500' : '' }}">
                                            {{ $user->rol_id == 1 ? 'Admin' : ($user->rol_id == 2 ? 'Recepción' : ($user->rol_id == 3 ? 'Groomer' : 'Cliente')) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($user->rol_id == 2 || $user->rol_id == 3)
                                            <div class="text-gray-900 dark:text-gray-200 font-semibold">{{ $user->especialidad ?? 'General' }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Turno: {{ $user->turno ?? 'No asignado' }}</div>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-600">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($user->estado)
                                            <span class="px-2 py-1 text-xs font-bold bg-green-900/50 text-green-400 border border-green-500 rounded-md">ACTIVO</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-bold bg-gray-900 text-gray-500 border border-gray-600 rounded-md">INACTIVO</span>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>