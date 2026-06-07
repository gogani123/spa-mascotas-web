<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            🛍️ Catálogo de Productos y Carrito (Módulo 8.1)
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
                    <p class="text-sm text-gray-300 mt-1 mb-3">Los siguientes productos requieren reabastecimiento urgente de stock:</p>
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

            <div class="mb-8 bg-white dark:bg-gray-800 p-4 rounded-lg border dark:border-gray-700 shadow flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('tienda.index') }}" class="px-4 py-2 text-xs font-bold rounded-lg uppercase tracking-wider transition {{ !request('categoria') ? 'bg-indigo-600 text-white' : 'bg-gray-900 text-gray-400 hover:bg-gray-700' }}">
                        🌐 Todo
                    </a>
                    @foreach($categorias ?? ['Alimentos', 'Accesorios', 'Higiene', 'Juguetes', 'Salud'] as $cat)
                        <a href="{{ route('tienda.index', ['categoria' => $cat]) }}" class="px-4 py-2 text-xs font-bold rounded-lg uppercase tracking-wider transition {{ request('categoria') == $cat ? 'bg-indigo-600 text-white' : 'bg-gray-900 text-gray-400 hover:bg-gray-700' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('tienda.index') }}" class="flex w-full md:w-72 gap-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por palabra clave..." class="w-full bg-gray-900 border-gray-700 text-white text-xs rounded-lg p-2 focus:ring-1 focus:ring-indigo-500">
                    <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-xs font-bold transition">🔍</button>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                        <h3 class="text-xl font-bold text-indigo-400">Productos Disponibles</h3>
                        @if(Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)
                            <a href="{{ route('admin.productos.crear') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-3 py-1.5 rounded text-xs uppercase tracking-wide transition">
                                ➕ Nuevo Producto
                            </a>
                        @endif
                    </div>
                                        
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($productos as $producto)
                            <div class="bg-white dark:bg-gray-800 rounded-lg border dark:border-gray-700 shadow flex flex-col justify-between hover:border-indigo-500 transition overflow-hidden">
                                
                                <div class="bg-gray-100 dark:bg-gray-950 h-44 w-full flex items-center justify-center relative border-b dark:border-gray-700 overflow-hidden">
                                    @if(!empty($producto->imagen_url))
                                        <img src="{{ asset('storage/' . $producto->imagen_url) }}" alt="{{ $producto->nombre }}" class="object-cover h-full w-full">
                                    @else
                                        <div class="text-center text-gray-500 select-none">
                                            <span class="text-3xl block">📦</span>
                                            <span class="text-[9px] uppercase font-mono tracking-wider">Sin foto referencial</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-5 flex-grow flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start">
                                            <span class="text-xs font-bold text-indigo-400 uppercase tracking-wider">{{ $producto->categoria }}</span>
                                            <span class="text-[10px] bg-gray-900 text-gray-400 px-2 py-0.5 rounded font-mono">ID: #{{ $producto->id }}</span>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-200 mt-1">{{ $producto->nombre }}</h4>
                                        <p class="text-3xl font-black text-emerald-400 mt-3">Bs. {{ number_format($producto->precio, 2) }}</p>
                                        <p class="text-sm text-gray-400 mt-1">Stock disponible: {{ $producto->stock }} unidades</p>
                                    </div>
                                    
                                    <form method="POST" action="{{ route('tienda.agregar', $producto->id) }}" class="mt-6">
                                        @csrf
                                        <button type="submit" class="w-full {{ $producto->stock <= 0 ? 'bg-gray-700 text-gray-500 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-500 text-white' }} py-2 font-bold rounded shadow transition text-xs uppercase tracking-wide" {{ $producto->stock <= 0 ? 'disabled' : '' }}>
                                            {{ $producto->stock <= 0 ? '🚫 Sin Existencias' : '+ Agregar al Carrito' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-2 p-8 text-center bg-gray-800 rounded border border-gray-700 text-gray-500 italic">
                                No se encontraron productos registrados en esta categoría del catálogo.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- BARRA LATERAL: MI CARRITO Y TOTALES FINANCIEROS (PUNTO 8.2) --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border dark:border-gray-700 shadow h-fit sticky top-6">
                    <h3 class="text-xl font-bold text-emerald-400 border-b border-gray-700 pb-2 mb-4">🛒 Mi Carrito</h3>
                    
                    @if(count($carrito) > 0)
                        <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-1">
                            @foreach($carrito as $id => $detalle)
                                <div class="flex justify-between items-center bg-gray-900 p-3 rounded border border-gray-700">
                                    <div>
                                        <p class="text-sm font-bold text-gray-200">{{ $detalle['nombre'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $detalle['cantidad'] }}x Bs. {{ number_format($detalle['precio'], 2) }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-400 font-mono">Bs. {{ number_format($detalle['precio'] * $detalle['cantidad'], 2) }}</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- SECCIÓN DE TRABAJO FINANCIERO ADICIONADA (SUBTOTAL, DESCUENTOS Y TOTAL GENERAL) --}}
                        <div class="border-t border-gray-700 pt-4 mb-6 space-y-2 text-sm text-gray-300">
                            <div class="flex justify-between">
                                <span>Subtotal Parcial:</span>
                                <span class="font-mono font-bold text-gray-100">Bs. {{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if(isset($descuento) && $descuento > 0)
                                <div class="flex justify-between text-amber-400 font-medium">
                                    <span>Cupón Aplicado ({{ $descuento }}%):</span>
                                    <span class="font-mono">- Bs. {{ number_format($monto_descuento, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-base font-black text-white border-t border-dashed border-gray-700 pt-2">
                                <span>Total a Pagar:</span>
                                <span class="font-mono text-emerald-400">Bs. {{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        {{-- BOTONES DE ACCIÓN COMPLETOS PARA LA INTERFAZ --}}
                        <div class="space-y-3">
                            {{-- BOTÓN COMPLETADO DE WHATSAPP --}}
                            <button type="button" onclick="enviarWhatsApp()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded shadow transition text-xs uppercase tracking-wider text-center block">
                                💬 Enviar Pedido por WhatsApp
                            </button>

                            <form method="POST" action="{{ route('tienda.comprar') }}">
                                @csrf
                                <input type="hidden" name="metodo_pago" value="Efectivo">
                                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded shadow transition text-xs uppercase tracking-wider">
                                    💵 Registrar Venta Presencial
                                </button>
                            </form>

                            <form method="POST" action="{{ route('tienda.vaciar') }}" class="pt-2 text-center">
                                @csrf
                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 underline uppercase tracking-wide">
                                    🗑️ Vaciar Carrito
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 text-sm">Tu carrito está vacío.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- INTERPRETACIÓN DINÁMICA DEL PEDIDO HACIA MENSAJERÍA AUTOMÁTICA --}}
    <script>
        function enviarWhatsApp() {
            // Mapeamos de forma nativa e inmune los datos que procesó PHP en la sesión
            let carrito = @json($carrito);
            let subtotal = "{{ number_format($subtotal, 2) }}";
            let descuento = "{{ $descuento ?? 0 }}";
            let montoDescuento = "{{ number_format($monto_descuento ?? 0, 2) }}";
            let total = "{{ number_format($total, 2) }}";
            
            let mensaje = "🐾 *NUEVO PEDIDO - SPA MASCOTAS* 🐾\n";
            mensaje += "========================================\n\n";
            
            // Recorremos los ítems y estructuramos el bloque de texto plano para WhatsApp
            Object.keys(carrito).forEach(function(key) {
                let item = carrito[key];
                let itemTotal = (item.precio * item.cantidad).toFixed(2);
                mensaje += `📦 *${item.nombre}*\n`;
                mensaje += `   ${item.cantidad}x Bs. ${parseFloat(item.precio).toFixed(2)}  =  *Bs. ${itemTotal}*\n\n`;
            });
            
            mensaje += "========================================\n";
            mensaje += `🔹 *Subtotal:* Bs. ${subtotal}\n`;
            if (parseInt(descuento) > 0) {
                mensaje += `🔸 *Descuento (${descuento}%):* - Bs. ${montoDescuento}\n`;
            }
            mensaje += `💰 *TOTAL GENERAL:* Bs. ${total}\n\n`;
            mensaje += "Por favor, confírmenme el pedido para proceder con la entrega. ¡Muchas gracias! 😊";

            let numeroSpa = "59170000000"; // Número por defecto para Bolivia (Prefijo 591)
            window.open("https://api.whatsapp.com/send?phone=" + numeroSpa + "&text=" + encodeURIComponent(mensaje), '_blank');
        }
    </script>
</x-app-layout>