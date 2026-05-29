<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Agendar Nueva Cita para mi Mascota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 bg-red-900 border-l-4 border-red-500 p-4 rounded-md shadow-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-300">Hubo un problema de disponibilidad:</h3>
                            <ul class="mt-1 text-sm text-red-200 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-900 border border-green-700 text-green-300 rounded-md font-bold text-center">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg border dark:border-gray-700 p-8">
                <form method="POST" action="{{ route('citas.store') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">¿Para quién es la cita? *</label>
                        <select name="mascota_id" class="w-full bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">-- Selecciona una de tus mascotas --</option>
                            @foreach($mascotas as $mascota)
                                <option value="{{ $mascota->id }}">{{ $mascota->nombre }}</option>
                            @endforeach
                        </select>
                        @if($mascotas->isEmpty())
                            <p class="text-xs text-red-400 mt-2">No tienes mascotas registradas. Ve a "Registrar Mascota" primero.</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Servicio Deseado *</label>
                        <select name="servicio_id" class="w-full bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">-- Selecciona un servicio del catálogo --</option>
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio->id }}">
                                    {{ $servicio->nombre }} (Dura: {{ $servicio->duracion_base }} min) - {{ number_format($servicio->precio, 2) }} Bs.
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            {{ Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2 ? 'Asignar Groomer (Peluquero) *' : 'Preferencia de Groomer (Opcional)' }}
                        </label>
                        <select name="groomer_id" class="w-full bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" {{ Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2 ? 'required' : '' }}>
                            <option value="" selected>
                                {{ Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2 ? '-- Selecciona al estilista encargado --' : 'Sin preferencia (El sistema asignará uno disponible)' }}
                            </option>
                            @foreach($groomers as $groomer)
                                <option value="{{ $groomer->id }}">✂️ {{ $groomer->name }}</option>
                            @endforeach
                        </select>
                        @if(Auth::user()->rol_id == 4)
                            <p class="text-xs text-gray-500 mt-1">Si tu mascota tiene un estilista favorito, elígelo aquí.</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Fecha de la Cita *</label>
                            <input type="date" name="fecha" min="{{ date('Y-m-d') }}" class="w-full bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1">Hora de Inicio *</label>
                            <select name="hora_inicio" id="hora_inicio" class="w-full bg-gray-900 border-gray-700 text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="" disabled selected>Seleccione la hora...</option>
                                @php
                                    $start = strtotime('08:00');
                                    $end = strtotime('19:30');
                                @endphp
                                @for ($i = $start; $i <= $end; $i += 1800)
                                    <option value="{{ date('H:i', $i) }}">⏰ {{ date('H:i', $i) }} hrs</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Agendar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const groomerSelect  = document.querySelector('select[name="groomer_id"]');
        const fechaInput     = document.querySelector('input[name="fecha"]');
        const mascotaSelect  = document.querySelector('select[name="mascota_id"]');
        const servicioSelect = document.querySelector('select[name="servicio_id"]');
        const horaSelect     = document.getElementById('hora_inicio');

        function consultarDisponibilidad() {
            if (!groomerSelect.value || !fechaInput.value || !mascotaSelect.value || !servicioSelect.value) {
                return;
            }

            horaSelect.innerHTML = '<option value="">🔍 Buscando espacios disponibles en la agenda...</option>';

            fetch(`/api/horarios-disponibles?fecha=${fechaInput.value}&groomer_id=${groomerSelect.value}&mascota_id=${mascotaSelect.value}&servicio_id=${servicioSelect.value}`)
                .then(response => {
                    return response.json().then(data => {
                        if (!response.ok) {
                            // Lanza el error del backend al catch
                            throw new Error(data.error || "Fallo interno de ejecución");
                        }
                        return data;
                    });
                })
                .then(data => {
                    horaSelect.innerHTML = '';
                    
                    if (data.horarios.length === 0) {
                        horaSelect.innerHTML = '<option value="">❌ El Groomer no tiene disponibilidad para esta fecha</option>';
                        return;
                    }

                    data.horarios.forEach(hora => {
                        horaSelect.innerHTML += `<option value="${hora}">🕒 ${hora} (Turno: ${data.turno})</option>`;
                    });
                })
                .catch(error => {
                    console.error("Error detectado:", error);
                    // Muestra el nombre exacto de la columna rota directamente en la interfaz
                    horaSelect.innerHTML = `<option value="">❌ Error: ${error.message}</option>`;
                });
        }

        [groomerSelect, fechaInput, mascotaSelect, servicioSelect].forEach(element => {
            if(element) element.addEventListener('change', consultarDisponibilidad);
        });

        setTimeout(consultarDisponibilidad, 200);
    });
</script>