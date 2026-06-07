<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            🧮 Pasarela de Facturación y Cobro (Módulo 8.3)
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-900 border border-red-700 text-red-300 rounded-md font-bold text-center text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-indigo-400 mb-4 border-b border-gray-700 pb-2">📦 Resumen del Servicio</h3>
                
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-300 mb-6 bg-gray-900 p-4 rounded border border-gray-700 font-mono">
                    <div>
                        <p><span class="text-gray-500">Propietario:</span> {{ $cita->cliente->name }}</p>
                        <p><span class="text-gray-500">Mascota:</span> {{ $cita->mascota->nombre }}</p>
                    </div>
                    <div>
                        <p><span class="text-gray-500">Servicio:</span> {{ $cita->servicio->nombre }}</p>
                        <p><span class="text-gray-500">Costo Base:</span> Bs. {{ number_format($cita->total, 2) }}</p>
                    </div>
                </div>

                {{-- FORMULARIO DE COBRO CON DESCUENTO INCORPORADO --}}
                <form method="POST" action="{{ route('citas.pagar', $cita->id) }}" class="space-y-6">
                    @csrf

                    {{-- SELECTOR DE MÉTODO DE PAGO --}}
                    <div>
                        <label for="metodo_pago" class="block text-xs font-bold uppercase text-gray-400 tracking-wide mb-2">💵 Método de Pago Recibido:</label>
                        <select name="metodo_pago" id="metodo_pago" required class="w-full bg-gray-900 border-gray-700 rounded-md text-white text-sm focus:ring-1 focus:ring-indigo-500">
                            <option value="Efectivo" {{ old('metodo_pago') == 'Efectivo' ? 'selected' : '' }}>💵 Efectivo en Ventanilla</option>
                            <option value="QR" {{ old('metodo_pago') == 'QR' ? 'selected' : '' }}>📱 Código QR Digital</option>
                            <option value="Transferencia" {{ old('metodo_pago') == 'Transferencia' ? 'selected' : '' }}>🏦 Transferencia Bancaria</option>
                        </select>
                    </div>

                    {{-- CAMPO NUEVO: DESCUENTO MANUAL EN CHECKOUT --}}
                    <div>
                        <label for="descuento_manual" class="block text-xs font-bold uppercase text-gray-400 tracking-wide mb-2">🎁 Descuento de Último Momento (Bs.):</label>
                        <input type="number" step="0.01" name="descuento_manual" id="descuento_manual" value="{{ old('descuento_manual', 0) }}" min="0" max="{{ $cita->total }}" oninput="calcularTotalNeto()" class="w-full bg-gray-900 border-gray-700 rounded-md text-white font-mono text-sm focus:ring-1 focus:ring-indigo-500" placeholder="0.00">
                        <small class="text-gray-500 text-[11px] mt-1 block">Incorpore un valor numérico si desea rebajar el costo del checkout (Cortesías, convenios, etc.).</small>
                    </div>

                    {{-- VISUALIZADOR DINÁMICO DE TOTAL COBRADO --}}
                    <div class="bg-indigo-950/40 border border-indigo-700/50 p-4 rounded-md flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-300 uppercase tracking-wide">Monto Neto a Recaudar:</span>
                        <span class="text-2xl font-black text-emerald-400 font-mono" id="contenedor_total_neto">Bs. {{ number_format($cita->total, 2) }}</span>
                    </div>

                    {{-- BOTONES DE CONTROL DE CAJA --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded text-xs font-bold uppercase transition">
                            ❌ Cancelar
                        </a>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded text-xs font-bold uppercase transition tracking-wider shadow">
                            💾 Confirmar Pago y Emitir Recibo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- INTERACTIVIDAD EN TIEMPO REAL EN EL FRONTEND --}}
    <script>
        function calcularTotalNeto() {
            // Pasamos el costo original guardado en el backend
            let costoOriginal = parseFloat("{{ $cita->total }}");
            let inputDescuento = document.getElementById('descuento_manual').value;
            
            // Si el input está vacío o es inválido, asumimos cero
            let descuento = inputDescuento ? parseFloat(inputDescuento) : 0;
            
            // Validar que el descuento no perfore el piso ni supere el techo comercial
            if(descuento < 0) descuento = 0;
            if(descuento > costoOriginal) descuento = costoOriginal;

            let totalNeto = costoOriginal - descuento;

            // Renderizar el cambio financiero inmediatamente
            document.getElementById('contenedor_total_neto').innerText = "Bs. " + totalNeto.toFixed(2);
        }
    </script>
</x-app-layout>