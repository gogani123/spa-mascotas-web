<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Calendario de Reprogramación Interactiva
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg border dark:border-gray-700">
                
                <div class="mb-4 flex justify-between items-center">
                    <p class="text-sm text-gray-400">💡 <strong class="text-indigo-400">Instrucciones:</strong> Haz clic sobre una cita, arrástrala al horario deseado y suéltala para reprogramar.</p>
                    <a href="{{ route('citas.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow text-sm transition">
                        Volver a la Lista
                    </a>
                </div>

                <div id='calendar' class="p-2"></div>

            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek', 
                slotMinTime: '08:00:00', 
                slotMaxTime: '20:00:00', 
                locale: 'es', 
                allDaySlot: false,
                editable: true, 
                events: '/api/citas-eventos', 
                
                eventDrop: function(info) {
                    let eventId = info.event.id;
                    let newStart = info.event.startStr;
                    let newEnd = info.event.endStr;

                    fetch(`/api/citas-mover/${eventId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ start: newStart, end: newEnd })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            console.log('Reprogramado exitosamente en BD.');
                        } else {
                            alert('Hubo un error al guardar.');
                            info.revert(); 
                        }
                    }).catch(() => {
                        alert('Error de conexión con el servidor.');
                        info.revert();
                    });
                }
            });
            
            calendar.render();
        });
    </script>

    <style>
        /* Cursor tipo "mano" para saber que es arrastrable */
        .fc-event { cursor: grab; }
        .fc-event:active { cursor: grabbing; }

        /* Variables de FullCalendar adaptadas a tu paleta oscura */
        :root {
            --fc-page-bg-color: transparent; /* Fondo transparente */
            --fc-neutral-bg-color: #1f2937; /* Gris secundario oscuro */
            --fc-neutral-text-color: #d1d5db; /* Texto principal claro */
            --fc-border-color: #374151; /* Color de las líneas separadoras */
            --fc-today-bg-color: rgba(79, 70, 229, 0.15); /* Resaltado sutil del día actual (Índigo) */
        }

        /* Color del Título (Ej: Mayo 2026) */
        .fc-toolbar-title {
            color: #f3f4f6 !important; 
            font-weight: 700 !important;
            text-transform: capitalize;
        }

        /* Días de la semana y Franjas Horarias (Ahora 100% legibles) */
        .fc-col-header-cell-cushion, 
        .fc-timegrid-slot-label-cushion, 
        .fc-timegrid-axis-cushion {
            color: #9ca3af !important; 
            text-decoration: none !important;
            font-weight: 600 !important;
        }

        /* Diseño de los Botones (Today, Prev, Next) */
        .fc-button-primary {
            background-color: #4f46e5 !important; /* Índigo de tu sistema */
            border: none !important;
            font-weight: bold !important;
            text-transform: capitalize !important;
            border-radius: 0.375rem !important; /* Bordes redondeados modernos */
            transition: all 0.2s !important;
        }
        .fc-button-primary:hover {
            background-color: #4338ca !important; /* Índigo más oscuro al pasar el mouse */
        }
        .fc-button-active {
            background-color: #3730a3 !important;
        }

        /* Ocultar subrayados molestos en los enlaces internos */
        .fc a { text-decoration: none !important; }
    </style>
</x-app-layout>