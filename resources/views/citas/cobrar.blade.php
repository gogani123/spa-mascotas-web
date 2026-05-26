<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Caja Registradora - Procesar Pago
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg border dark:border-gray-700">
                
                <div class="mb-6 text-center border-b border-gray-700 pb-6">
                    <h3 class="text-2xl font-bold text-emerald-400">Detalle de Cobro</h3>
                    <p class="text-gray-400 mt-2">Registrando el pago del servicio realizado.</p>
                </div>

                <div class="bg-gray-900 p-6 rounded-md mb-8">
                    <p class="text-gray-300 mb-2"><strong>Cliente:</strong> {{ $cita->cliente->name }}</p>
                    <p class="text-gray-300 mb-2"><strong>Mascota:</strong> {{ $cita->mascota->nombre }}</p>
                    <p class="text-gray-300 mb-2"><strong>Servicio:</strong> {{ $cita->servicio->nombre }}</p>
                    <p class="text-emerald-400 text-xl font-bold mt-4 border-t border-gray-700 pt-4">Total a Pagar: Bs. {{ number_format($cita->servicio->precio, 2) }}</p>
                </div>

                <form method="POST" action="{{ route('citas.pagar', $cita->id) }}">
                    @csrf
                    
                    <label class="block text-sm font-medium text-gray-400 mb-2">Método de Pago *</label>
                    <select name="metodo_pago" class="w-full bg-gray-900 border-gray-700 text-white rounded-md shadow-sm mb-8" required>
                        <option value="" disabled selected>Selecciona cómo está pagando el cliente...</option>
                        <option value="Efectivo">💵 Efectivo (Billetes / Monedas)</option>
                        <option value="QR">📱 Pago por QR Simple</option>
                        <option value="Transferencia">🏦 Transferencia Bancaria</option>
                    </select>

                    <div class="flex justify-between">
                        <a href="{{ route('citas.index') }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-500 text-white font-bold rounded-md shadow transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-8 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-md shadow transition">
                            CONFIRMAR PAGO
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>