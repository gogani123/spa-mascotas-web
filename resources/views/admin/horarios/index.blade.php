<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Configuración de Horario Laboral Global
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900 border border-green-700 text-green-300 rounded-md font-bold text-center">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-indigo-400">Jornada de Atención General</h3>
                    <p class="text-sm text-gray-400 mt-1">Define qué días abre el Spa de Mascotas y en qué rango de horas se podrán agendar citas en el calendario maestro.</p>
                </div>

                <form method="POST" action="{{ route('admin.horarios.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-300">
                            <thead class="bg-gray-900 text-xs uppercase text-indigo-400 border-b border-gray-700">
                                <tr>
                                    <th class="px-6 py-4">Día de la Semana</th>
                                    <th class="px-6 py-4 text-center">Estado del Spa</th>
                                    <th class="px-6 py-4">Hora Apertura</th>
                                    <th class="px-6 py-4">Hora Cierre</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($horarios as $horario)
                                    <tr class="hover:bg-gray-750 transition-colors">
        
                                        <td class="px-6 py-4 font-bold text-white">
                                            {{ $horario->nombre_dia }}
                                        </td>
                                        
                                        <td class="px-6 py-4 text-center">
                                            <select name="horarios[{{ $horario->id }}][abierto]" class="bg-gray-900 border-gray-700 {{ $horario->abierto ? 'text-green-400' : 'text-red-400' }} font-bold rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="1" {{ $horario->abierto ? 'selected' : '' }} class="text-green-400">Abierto</option>
                                                <option value="0" {{ !$horario->abierto ? 'selected' : '' }} class="text-red-400">Cerrado</option>
                                            </select>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <select name="horarios[{{ $horario->id }}][hora_apertura]" class="bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                @for ($i = 6; $i <= 22; $i++)
                                                    @foreach (['00', '30'] as $min)
                                                        @php $horaFormateada = str_pad($i, 2, '0', STR_PAD_LEFT) . ':' . $min; @endphp
                                                        <option value="{{ $horaFormateada }}" {{ \Carbon\Carbon::parse($horario->hora_apertura)->format('H:i') == $horaFormateada ? 'selected' : '' }}>
                                                            {{ $horaFormateada }}
                                                        </option>
                                                    @endforeach
                                                @endfor
                                            </select>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <select name="horarios[{{ $horario->id }}][hora_cierre]" value="{{ \Carbon\Carbon::parse($horario->hora_cierre)->format('H:i') }}" class="bg-gray-900 border-gray-700 text-white rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                                @for ($i = 6; $i <= 22; $i++)
                                                    @foreach (['00', '30'] as $min)
                                                        @php $horaFormateada = str_pad($i, 2, '0', STR_PAD_LEFT) . ':' . $min; @endphp
                                                        <option value="{{ $horaFormateada }}" {{ \Carbon\Carbon::parse($horario->hora_cierre)->format('H:i') == $horaFormateada ? 'selected' : '' }}>
                                                            {{ $horaFormateada }}
                                                        </option>
                                                    @endforeach
                                                @endfor
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="px-10 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-lg transition">
                            Guardar Configuración Operativa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>