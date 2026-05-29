<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Catálogo de Productos y Carrito
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-900 border border-emerald-700 text-emerald-300 rounded-md font-bold text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-900 border border-red-700 text-red-300 rounded-md font-bold text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            @if(isset($alertasStock) && $alertasStock->count() > 0)
                <div class="mb-8 p-5 bg-red-900/40 border-l-4 border-red-600 rounded shadow-md">
                    <h4 class="font-bold flex items-center gap-2 text-red-400 text-lg">
                        ⚠️ Alerta de Inventario Crítico
                    </h4>
                    <p class="text-sm text-gray-300 mt-1 mb-3">Los siguientes productos requieren reabastecimiento urgente:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($alertasStock as $alerta)
                            <div class="bg-gray-800 border border-red-700 p-2 rounded text-sm text-gray-200 flex justify-between items-center">
                                <span>{{ $alerta->nombre }}</span>
                                <span class="bg-red-600 text-white px-2 py-0.5 rounded text-xs font-bold">{{ $alerta->stock }} unid.</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <h3 class="text-xl font-bold text-indigo-400 border-b border-gray-700 pb-2">Productos Disponibles</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($productos as $producto)
                            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg border dark:border-gray-700 shadow flex flex-col justify-between hover:border-indigo-500 transition">
                                <div>
                                    <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">{{ $producto->categoria }}</span>
                                    <h4 class="text-lg font-bold text-gray-200 mt-1">{{ $producto->nombre }}</h4>
                                    <p class="text-3xl font-black text-emerald-400 mt-3">Bs. {{ number_format($producto->precio, 2) }}</p>
                                    <p class="text-sm text-gray-400 mt-1">Stock disponible: {{ $producto->stock }}</p>
                                </div>
                                
                                <form method="POST" action="{{ route('tienda.agregar', $producto->id) }}" class="mt-6">
                                    @csrf
                                    <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded shadow transition">
                                        + Agregar al Carrito
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border dark:border-gray-700 shadow h-fit sticky top-6">
                    <h3 class="text-xl font-bold text-emerald-400 border-b border-gray-700 pb-2 mb-4">🛒 Mi Carrito</h3>
                    
                    @if(count($carrito) > 0)
                        <div class="space-y-4 mb-6">
                            @foreach($carrito as $id => $detalle)
                                <div class="flex justify-between items-center bg-gray-900 p-3 rounded border border-gray-700">
                                    <div>
                                        <p class="text-sm font-bold text-gray-200">{{ $detalle['nombre'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $detalle['cantidad'] }}x Bs. {{ number_format($detalle['precio'], 2) }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-400">Bs. {{ number_format($detalle['precio'] * $detalle['cantidad'], 2) }}</p>
                                </div>
                            @endforeach
                        </div>

                        @if($descuento == 0)
                            <form method="POST" action="{{ route('tienda.cupon') }}" class="mb-4 flex gap-2">
                                @csrf
                                <input type="text" name="codigo" placeholder="Ej: PETLOVER20" class="w-full bg-gray-900 border-gray-700 text-white rounded text-sm uppercase" required>
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 px-3 py-2 rounded text-white text-sm font-bold transition">Aplicar</button>
                            </form>
                        @else
                            <div class="mb-4 bg-green-900/50 border border-green-700 p-3 rounded text-center">
                                <p class="text-green-400 text-sm font-bold">🎉 ¡Cupón del {{ $descuento }}% Aplicado!</p>
                            </div>
                        @endif

                        <div class="border-t border-gray-700 pt-4 mb-6 space-y-2">
                            <div class="flex justify-between text-gray-400 text-sm">
                                <span>Subtotal:</span>
                                <span>Bs. {{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if($descuento > 0)
                            <div class="flex justify-between text-green-400 text-sm font-bold">
                                <span>Descuento ({{ $descuento }}%):</span>
                                <span>- Bs. {{ number_format($monto_descuento, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center text-xl font-black text-white pt-2 border-t border-gray-700">
                                <span>TOTAL:</span>
                                <span>Bs. {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <button onclick="enviarWhatsApp()" class="w-full py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded shadow transition flex items-center justify-center gap-2 text-lg">
                                📱 Pedir por WhatsApp
                            </button>

                            @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                                <div class="border-t border-gray-700 pt-4 mt-2">
                                    <form method="POST" action="{{ route('tienda.comprar') }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="text-[11px] text-indigo-300 block mb-1 font-bold uppercase tracking-wider">Registrar Pago en Mostrador:</label>
                                            <select name="metodo_pago" required class="w-full bg-gray-900 border-gray-700 text-white rounded text-sm focus:ring-indigo-500 focus:border-indigo-500 p-2">
                                                <option value="">-- Seleccione medio de cobro --</option>
                                                <option value="Efectivo">💵 Efectivo</option>
                                                <option value="QR">📱 Código QR</option>
                                                <option value="Transferencia">🏦 Transferencia Bancaria</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded shadow transition text-sm flex items-center justify-center gap-1">
                                            ✔ Confirmar Cobro y Entregar
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('tienda.vaciar') }}">
                                @csrf
                                <button type="submit" class="w-full py-2 bg-red-900/60 hover:bg-red-800 text-red-200 font-medium rounded shadow transition text-xs opacity-70 hover:opacity-100">
                                    🗑️ Vaciar Todo el Carrito
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">Tu carrito está vacío.</p>
                            <p class="text-sm text-gray-600 mt-2">Agrega productos del catálogo para comenzar.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <script>
        function enviarWhatsApp() {
            let mensaje = "🐾 *NUEVO PEDIDO - SPA MASCOTAS* 🐾\n\n";
            mensaje += "Hola, me gustaría realizar el siguiente pedido:\n\n";
            
            const carrito = @json($carrito);
            const subtotalJs = {{ $subtotal }};
            const descuentoJs = {{ $descuento }};
            const montoDescuentoJs = {{ $monto_descuento }};
            const totalJs = {{ $total }};
            
            for (const id in carrito) {
                const item = carrito[id];
                const subtotalItem = item.precio * item.cantidad;
                mensaje += "▪️ " + item.cantidad + "x " + item.nombre + " - Bs. " + subtotalItem.toFixed(2) + "\n";
            }
            
            if(descuentoJs > 0) {
                mensaje += "\n💳 *Subtotal:* Bs. " + subtotalJs.toFixed(2) + "\n";
                mensaje += "🎁 *Descuento (" + descuentoJs + "%):* -Bs. " + montoDescuentoJs.toFixed(2) + "\n";
            }

            mensaje += "\n💰 *TOTAL A PAGAR: Bs. " + totalJs.toFixed(2) + "*\n\n";
            mensaje += "Por favor, indíquenme los métodos de pago. ¡Gracias!";
            
            let numeroSpa = "59170000000"; 
            let url = "https://api.whatsapp.com/send?phone=" + numeroSpa + "&text=" + encodeURIComponent(mensaje);
            
            window.open(url, '_blank');
        }
    </script>
</x-app-layout>