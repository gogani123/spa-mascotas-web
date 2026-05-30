<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo - Spa de Mascotas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8 flex flex-col items-center">
    <div class="bg-white p-6 rounded-lg shadow-md w-96 border border-gray-300 font-mono" id="ticket">
        <div class="text-center mb-4">
            <h2 class="text-xl font-bold">SPA DE MASCOTAS</h2>
            <p class="text-xs text-gray-500">Comprobante de Pago Electrónico</p>
            <p class="text-xs">Fecha: {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
        </div>
        <hr class="border-dashed border-gray-400 my-2">
        <div class="text-sm space-y-1">
            <p><strong>Cliente:</strong> {{ $cita->cliente->name ?? 'Consumidor Final' }}</p>
            <p><strong>Mascota:</strong> {{ $cita->mascota->nombre ?? 'N/A' }} ({{ $cita->mascota->tamano ?? 'Estándar' }})</p>
            <p><strong>Atendido por:</strong> {{ $cita->groomer->name ?? 'Personal asignado' }}</p>
        </div>
        <hr class="border-dashed border-gray-400 my-2">
        <div class="text-sm">
            <div class="flex justify-between font-bold">
                <span>CONCEPTO</span>
                <span>TOTAL</span>
            </div>
            <div class="flex justify-between text-gray-700 mt-1">
                <span>{{ $cita->servicio->nombre ?? 'Servicio Estética' }}</span>
                <span>BOB {{ number_format($cita->total ?? ($cita->servicio->precio ?? 0), 2) }}</span>
            </div>
        </div>
        <hr class="border-dashed border-gray-400 my-2">
        <div class="space-y-1 text-sm text-right">
            <p><strong>Método de pago:</strong> {{ $cita->metodo_pago ?? 'Efectivo' }}</p>
            <p class="text-lg font-bold text-emerald-600">Total Pagado: BOB {{ number_format($cita->total ?? ($cita->servicio->precio ?? 0), 2) }}</p>
        </div>
        <div class="text-center text-xs text-gray-500 mt-6">
            <p>¡Gracias por confiar en nosotros!</p>
            <p>Mascotas felices, dueños tranquilos.</p>
        </div>
    </div>
    <div class="mt-4 flex gap-3 print:hidden">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 font-bold">Imprimir Recibo</button>
        <a href="{{ route('citas.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700 font-bold">Volver</a>
    </div>
</body>
</html>