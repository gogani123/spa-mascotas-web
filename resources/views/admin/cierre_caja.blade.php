<x-app-layout>
    <!-- Cargamos la librería de gráficos Chart.js desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12 bg-gray-900 min-h-screen text-white">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('citas.index') }}" class="text-gray-400 hover:text-white transition font-bold text-sm flex items-center gap-1">
                    ⬅ Volver al listado de citas
                </a>
            </div>

            <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-bold mb-2 flex items-center gap-2">
                    <span>🏦</span> Consolidación Diario y Cierre de Caja
                </h2>
                
                <p class="text-gray-400 text-sm mb-6">Fecha de cuadre: <span class="text-indigo-400 font-bold">{{ $hoy }}</span></p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">

                <h3 class="text-md font-bold mb-3 text-gray-300">Desglose Consolidado por Métodos de Pago</h3>
                <div class="bg-gray-900 border border-gray-700 rounded-xl overflow-hidden mb-8">
                    <table class="w-full text-left text-sm text-gray-400">
                        <thead class="bg-gray-800 text-xs text-gray-300 uppercase font-mono border-b border-gray-700">
                            <tr>
                                <th class="px-6 py-3">Método de Pago</th>
                                <th class="px-6 py-3 text-center">Cantidad de Citas</th>
                                <th class="px-6 py-3 text-right">Subtotal Recaudado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @forelse($consolidadoMetodos as $metodo)
                                @php $metodo = (object) $metodo; @endphp
                                <tr class="hover:bg-gray-800/30">
                                    <td class="px-6 py-4 font-semibold text-white">💼 {{ $metodo->metodo_pago ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-center">{{ $metodo->cantidad ?? 0 }}</td>
                                    <td class="px-6 py-4 text-right text-emerald-400 font-bold">BOB {{ number_format($metodo->total_metodo ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No se registraron cobros el día de hoy.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- 📊 SECCIÓN NUEVA: GRÁFICO DE RANKING DE RENTABILIDAD / TOP SERVICIOS -->
                <hr class="border-gray-700 my-6">
                <h3 class="text-md font-bold mb-4 text-gray-300 flex items-center gap-2">
                    <span>📈</span> Ranking de Rentabilidad: Servicios Más Solicitados
                </h3>
                
                <div class="bg-gray-900 border border-gray-700 p-4 rounded-xl">
                    <canvas id="chartRanking" class="w-full" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de inicialización de Chart.js alimentado con Blade -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('chartRanking').getContext('2d');
            
            // Pasamos los datos del backend ordenados por Eloquent a arreglos de JavaScript
            const labelsServicios = {!! json_encode($rankingServicios->pluck('nombre')) !!};
            const datosVentas = {!! json_encode($rankingServicios->pluck('total_ventas')) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labelsServicios.length ? labelsServicios : ['Sin datos'],
                    datasets: [{
                        label: 'Cantidad de ejecuciones',
                        data: datosVentas.length ? datosVentas : [0],
                        backgroundColor: 'rgba(99, 102, 241, 0.6)', // Color Índigo con opacidad
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#374151' }, // Grid gris oscuro para el modo dark
                            ticks: { color: '#9ca3af', stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af' }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>