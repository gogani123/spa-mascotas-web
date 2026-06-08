<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Mascota;
use App\Models\Bloqueo;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Insumo;
use App\Models\SalidaInsumo;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class CitaController extends Controller
{
    // Función para mostrar la lista maestra de citas
    public function index()
    {
        $usuario = Auth::user();

        if ($usuario->rol_id == 1 || $usuario->rol_id == 2) {
            // Administradores ven TODO
            $citas = Cita::with(['cliente', 'mascota', 'servicio', 'groomer'])
                        ->orderBy('fecha', 'desc')->orderBy('hora_inicio', 'asc')->get();
        } elseif ($usuario->rol_id == 3) {
            // Groomer solo ve SUS citas
            $citas = Cita::where('groomer_id', $usuario->id)
                        ->with(['cliente', 'mascota', 'servicio'])
                        ->orderBy('fecha', 'asc')->orderBy('hora_inicio', 'asc')
                        ->get();
        } else {
            // Clientes solo ven SUS propias mascotas
            $citas = Cita::where('cliente_id', $usuario->id)
                        ->with(['mascota', 'servicio', 'groomer'])
                        ->orderBy('fecha', 'asc')->orderBy('hora_inicio', 'asc')
                        ->get();
        }

        return view('citas.index', compact('citas'));
    }

    // Función para mostrar el formulario de Agendar
    public function create()
    {
        $servicios = Servicio::orderBy('nombre', 'asc')->get();
        $usuario = Auth::user();
        
        // Traemos a los groomers para que el cliente pueda elegir su preferencia
        $groomers = User::where('rol_id', 3)->get(); 

        if ($usuario->rol_id == 1 || $usuario->rol_id == 2) {
            $mascotas = Mascota::orderBy('nombre', 'asc')->get();
        } else {
            $mascotas = Mascota::where('user_id', $usuario->id)->get();
        }

        return view('citas.create', compact('servicios', 'mascotas', 'groomers')); 
    }

    public function store(Request $request)
    {
        // 1. Validaciones iniciales del formulario
        $request->validate([
            'mascota_id'  => 'required|exists:mascotas,id',
            'servicio_id' => 'required|exists:servicios,id',
            'groomer_id'  => 'required|exists:users,id',
            'fecha'       => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
        ]);

        // 2. Obtener los modelos de la base de datos
        $servicio = \App\Models\Servicio::findOrFail($request->servicio_id);
        $mascota  = \App\Models\Mascota::findOrFail($request->mascota_id);

        // 3. ALGORITMO: Calcular duración exacta en base a la mascota (Rúbrica de Duración)
        $duracionTotal = $servicio->duracion_minutos ?? 45; 

        // Incremento por tamaño
        $tamanoMascota = $mascota->tamano ?? $mascota->tamaño ?? 'Pequeño';
        if ($tamanoMascota === 'Mediano') { $duracionTotal += 10; }
        elseif ($tamanoMascota === 'Grande') { $duracionTotal += 15; }
        elseif ($tamanoMascota === 'Gigante') { $duracionTotal += 30; }

        // Incremento por comportamiento/temperamento
        if ($mascota->comportamiento === 'Nervioso' || $mascota->comportamiento === 'Agresivo') {
            $duracionTotal += 20; 
        }

        // 4. Calcular hora_fin exacta
        $horaInicioCarbon = \Carbon\Carbon::createFromFormat('H:i', $request->hora_inicio);
        $horaFinCarbon    = clone $horaInicioCarbon;
        $horaFinCarbon->addMinutes($duracionTotal);

        $hora_inicio_str = $horaInicioCarbon->format('H:i:s');
        $hora_fin_str    = $horaFinCarbon->format('H:i:s');

        // 5. ALGORITMO ANTI-CRUCE: Verificar si el Groomer ya tiene un espacio ocupado
        $cruceCita = \App\Models\Cita::where('groomer_id', $request->groomer_id)
            ->where('fecha', $request->fecha)
            ->where('estado', '!=', 'Cancelada') 
            ->where(function ($query) use ($hora_inicio_str, $hora_fin_str) {
                $query->where('hora_inicio', '<', $hora_fin_str)
                    ->where('hora_fin', '>', $hora_inicio_str);
            })
            ->first();

        if ($cruceCita) {
            return back()->withInput()->withErrors([
                'hora_inicio' => "🚫 El estilista (Groomer) seleccionado ya tiene una cita agendada de {$cruceCita->hora_inicio} a {$cruceCita->hora_fin}. Por favor, elige otro horario."
            ]);
        }

        // ====================================================================
        // VALIDACIÓN DE SHIFT/TURNO INMUNE A CASING
        // ====================================================================
        $groomerObj = \App\Models\User::find($request->groomer_id);
        $turnoRaw = $groomerObj->turno ?? 'completo';

        $turnoNormalized = strtolower(trim($turnoRaw));
        $horaComparar = \Carbon\Carbon::parse($hora_inicio_str)->format('H:i');

        if (($turnoNormalized === 'mañana' || $turnoNormalized === 'manana') && $horaComparar >= '13:00') {
            return back()->withInput()->withErrors([
                'hora_inicio' => "🛑 Operación rechazada: El estilista seleccionado trabaja únicamente en el turno de la Mañana (09:00 a 13:00)."
            ]);
        }

        if ($turnoNormalized === 'tarde' && $horaComparar < '14:00') {
            return back()->withInput()->withErrors([
                'hora_inicio' => "🛑 Operación rechazada: El estilista seleccionado trabaja únicamente en el turno de la Tarde (14:00 a 18:00)."
            ]);
        }
        // ====================================================================

        // Determinar el ID del propietario real de la mascota
        $idCliente = auth()->user()->rol_id == 4 ? auth()->id() : ($mascota->user_id ?? $mascota->cliente_id);

        // Regla de Negocio (Módulo 8.3): Descuento Automatizado de Cliente Frecuente
        $citasPrevias = \App\Models\Cita::where('cliente_id', $idCliente)->where('estado', 'Completada')->count();
        $precioCobrar = $servicio->precio;
        $mensajeFidelidad = '';

        if ($citasPrevias >= 3) {
            $precioCobrar = $servicio->precio * 0.90;
            $mensajeFidelidad = ' 🌟 ¡Beneficio de Cliente Frecuente activado! Se aplicó un 10% de descuento automático.';
        }

        // 6. Crear la cita en la base de datos PostgreSQL
        $cita = \App\Models\Cita::create([
            'cliente_id'  => $idCliente,
            'mascota_id'  => $request->mascota_id,
            'servicio_id' => $request->servicio_id,
            'groomer_id'  => $request->groomer_id,
            'fecha'       => $request->fecha,
            'hora_inicio' => $hora_inicio_str,
            'hora_fin'    => $hora_fin_str,
            'estado'      => auth()->user()->rol_id == 4 ? 'Pendiente' : 'Confirmada', 
            'total'       => $precioCobrar, 
        ]);

        // ====================================================================
        // ⚡ NUEVO DISPARADOR: AUTOMATIZACIÓN DE MENSAJERÍA (PUNTO 9)
        // ====================================================================
        // Si la cita fue solicitada por un Cliente por autogestión web, se le notifica que entró en revisión
        if (auth()->user()->rol_id == 4) {
            \App\Services\NotificacionService::notificarSolicitudEnRevision($cita);
        }
        // ====================================================================

        return redirect()->route('citas.index')->with('success', '📅 ¡Cita agendada correctamente! El sistema calculó el espacio de tiempo necesario de forma automática.' . $mensajeFidelidad);
    }
    
    // MÓDULO DE COBRANZA (PUNTO 3.1)
    public function cobrar(Cita $cita)
    {
        return view('citas.cobrar', compact('cita'));
    }

    public function pagar(Request $request, Cita $cita)
    {
        $request->validate([
            'metodo_pago' => 'required|string|in:Efectivo,QR,Transferencia',
            'descuento_manual' => 'nullable|numeric|min:0',
        ]);

        $descuentoManual = $request->input('descuento_manual', 0);

        if ($descuentoManual > $cita->total) {
            return back()->withInput()->withErrors([
                'descuento_manual' => '🛑 Error operativo: El descuento no puede ser superior al costo del servicio.'
            ]);
        }

        $totalDefinitivo = $cita->total - $descuentoManual;

        $cita->update([
            'estado_pago' => 'Pagado',
            'metodo_pago' => $request->metodo_pago,
            'total'       => $totalDefinitivo, 
        ]);

        // ====================================================================
        // ⚡ DISPARADOR AUTOMÁTICO DE NOTIFICACIÓN DE PAGO (PUNTO 9)
        // ====================================================================
        $conceptoServicio = "Servicio de Grooming para " . $cita->mascota->nombre;
        \App\Services\NotificacionService::notificarPagoRegistrado(
            $cita->cliente_id, 
            $totalDefinitivo, 
            $conceptoServicio, 
            $request->metodo_pago
        );
        // ====================================================================

        return redirect()->route('citas.index')->with('success', '¡Pago registrado correctamente!');
    }

    // CALENDARIO INTERACTIVO (PUNTO 3.1)
    public function calendario()
    {
        return view('citas.calendario');
    }

    public function apiEventos()
    {
        $citas = Cita::with(['mascota', 'cliente'])->get();
        $eventos = [];
        
        foreach($citas as $cita) {
            $eventos[] = [
                'id' => $cita->id,
                'title' => $cita->mascota->nombre . ' (' . $cita->cliente->name . ')',
                'start' => $cita->fecha . 'T' . $cita->hora_inicio,
                'end' => $cita->fecha . 'T' . $cita->hora_fin,
                'color' => $cita->estado_pago == 'Pagado' ? '#059669' : '#4f46e5', 
            ];
        }
        
        return response()->json($eventos);
    }

    public function apiMover(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $cita->update([
            'fecha' => $start->format('Y-m-d'),
            'hora_inicio' => $start->format('H:i'),
            'hora_fin' => $end->format('H:i'),
        ]);

        return response()->json(['success' => true]);
    }

    public function aprobar(Cita $cita)
    {
        // 1. Validar la seguridad operativa del rol que intenta aprobar
        if (Auth::user()->rol_id == 4) {
            return redirect()->back()->withErrors(['error' => 'Acceso denegado. Solo el personal de Recepción o Administración puede confirmar citas.']);
        }

        // 2. Cambiar el estado de la cita en la base de datos
        $cita->update(['estado' => 'Confirmada']);

        // ====================================================================
        // ⚡ NUEVO DISPARADOR AUTOMÁTICO: CONFIRMACIÓN EN TIEMPO REAL (PUNTO 9)
        // ====================================================================
        // Invocamos el servicio para guardar la alerta y despachar el correo electrónico de inmediato
        \App\Services\NotificacionService::notificarCitaConfirmada($cita);
        // ====================================================================

        return redirect()->back()->with('success', '¡Cita de ' . $cita->mascota->nombre . ' aprobada correctamente! Se ha enviado una notificación de confirmación al cliente.');
    }

    // app/Http/Controllers/CitaController.php

    public function atender(Cita $cita)
    {
        // 1. Seguridad: Verificar que el Groomer sea el dueño de la cita
        if (Auth::user()->rol_id == 3 && $cita->groomer_id != Auth::user()->id) {
            return redirect()->route('citas.index')->withErrors(['error' => 'No tienes permiso para atender esta cita.']);
        }

        // 2. NUEVO: Traer todos los insumos disponibles en el almacén global del Spa (Para el combo/select)
        $insumosDisponibles = \App\Models\Insumo::orderBy('nombre', 'asc')->get();

        // 3. NUEVO: Traer los insumos que YA han sido registrados o entregados para esta cita específica
        $insumosEntregados = \App\Models\SalidaInsumo::where('cita_id', $cita->id)
            ->with('insumo') // Cargamos la relación para leer el nombre del insumo
            ->get();

        // 4. Enviamos la cita, el catálogo de insumos y lo ya entregado directamente a la vista
        return view('citas.atender', compact('cita', 'insumosDisponibles', 'insumosEntregados'));
    }
    // PEGA EL NUEVO MÉTODO JUSTO AQUÍ:
    public function registrarSalida(Request $request, $citaId)
    {
        $request->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad_entregada' => 'required|integer|min:1',
        ]);

        $cita = \App\Models\Cita::findOrFail($citaId);
        $insumo = \App\Models\Insumo::findOrFail($request->insumo_id);

        // 1. Validar disponibilidad de stock real antes de prestar el suministro
        if ($insumo->cantidad_disponible < $request->cantidad_entregada) {
            return response()->json(['error' => 'Stock insuficiente en el almacén central.'], 422);
        }

        // 2. Transacción de Base de Datos para garantizar consistencia absoluta
        DB::transaction(function () use ($request, $cita, $insumo) {
            // Restar existencias del almacén global de inmediato
            $insumo->decrement('cantidad_disponible', $request->cantidad_entregada);

            // Verificar si ya se le había entregado este mismo insumo en la misma sesión
            $salidaExistente = \App\Models\SalidaInsumo::where('cita_id', $cita->id)
                ->where('insumo_id', $request->insumo_id)
                ->where('estado', 'Entregado')
                ->first();

            if ($salidaExistente) {
                $salidaExistente->cantidad_entregada += $request->cantidad_entregada;
                $salidaExistente->save();
                $salida = $salidaExistente;
            } else {
                $salida = \App\Models\SalidaInsumo::create([
                    'cita_id' => $cita->id,
                    'insumo_id' => $request->insumo_id,
                    'groomer_id' => $cita->groomer_id ?? Auth::id(),
                    'cantidad_entregada' => $request->cantidad_entregada,
                    'cantidad_usada' => 0,
                    'cantidad_devuelta' => 0,
                    'estado' => 'Entregado',
                    'fecha_salida' => now('America/La_Paz'),
                ]);
            }

            // Registrar la operación en la auditoría del Spa
            \App\Models\AuditLog::create([
                'usuario_id' => Auth::user()->id,
                'accion' => 'despachar_insumo_servicio',
                'modelo' => 'SalidaInsumo',
                'modelo_id' => $salida->id,
                'datos_antiguos' => null,
                'datos_nuevos' => json_encode($salida),
            ]);
        });

        // Respondemos con éxito en JSON para que el script 'fetch' de tu vista recargue la página suavemente
        return response()->json(['success' => true]);
    }

    public function completar(Request $request, $id)
    {
        $cita = \App\Models\Cita::findOrFail($id);

        $request->validate([
            'estado_inicial' => 'required|string',
            'foto_antes'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_despues'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 1. Procesamiento de archivos de evidencia visual (Punto 6)
        $rutaAntes = null;
        if ($request->hasFile('foto_antes')) {
            $rutaAntes = $request->file('foto_antes')->store('grooming', 'public');
        }

        $rutaDespues = null;
        if ($request->hasFile('foto_despues')) {
            $rutaDespues = $request->file('foto_despues')->store('grooming', 'public');
        }

        // ====================================================================
        // INTERSECCIÓN 7.2: DESCUENTO Y LOGICA DE INSUMOS AUTOMÁTICA
        // ====================================================================
        if ($request->has('insumo_estado')) {
            DB::transaction(function () use ($request) {
                foreach ($request->insumo_estado as $salidaId => $estadoFinal) {
                    // Buscamos el registro de entrega de material asociado
                    $salida = \App\Models\SalidaInsumo::with('insumo')->find($salidaId);
                    
                    if ($salida && $salida->estado == 'Entregado') {
                        $cantidadUsada = 0;
                        $cantidadDevuelta = 0;

                        if ($estadoFinal === 'Usado') {
                            $cantidadUsada = $salida->cantidad_entregada;
                        } elseif ($estadoFinal === 'Desperdiciado') {
                            $cantidadUsada = $salida->cantidad_entregada; // Cuenta como merma consumida para control de costos
                        } elseif ($estadoFinal === 'Devuelto') {
                            $cantidadDevuelta = $salida->cantidad_entregada;
                            
                            // FUNCIONALIDAD DE DEVOLUCIÓN: Reincorporación inmediata al stock global del Spa
                            $salida->insumo->increment('cantidad_disponible', $cantidadDevuelta);
                        }

                        // Actualización del registro puente consolidando las cantidades definitivas
                        $salida->update([
                            'cantidad_usada' => $cantidadUsada,
                            'cantidad_devuelta' => $cantidadDevuelta,
                            'estado' => $estadoFinal,
                            'observaciones' => 'Consumo final cerrado automáticamente en el cierre definitivo del servicio.',
                        ]);

                        // Registro riguroso en Auditoría de Sistema (Logs)
                        \App\Models\AuditLog::create([
                            'usuario_id' => Auth::user()->id,
                            'accion' => 'cierre_inventario_servicio',
                            'modelo' => 'SalidaInsumo',
                            'modelo_id' => $salida->id,
                            'datos_antiguos' => json_encode(['estado' => 'Entregado']),
                            'datos_nuevos' => json_encode([
                                'estado' => $estadoFinal,
                                'usado' => $cantidadUsada,
                                'devuelto' => $cantidadDevuelta
                            ]),
                        ]);
                    }
                }
            });
        }
        // ====================================================================

        // 2. Guardar el Historial Clínico en la Ficha Técnica de Grooming
        $datosJson = [
            'tareas' => $request->checklist ?? [],
        ];

        \App\Models\FichaGrooming::updateOrCreate(
            ['cita_id' => $cita->id], 
            [
                'estado_ingreso' => $request->estado_inicial,
                'checklist_json' => json_encode($datosJson),
                'foto_antes'     => $rutaAntes,
                'foto_despues'   => $rutaDespues,
            ]
        );

        // 3. Actualizamos el estado de la cita a Completada
        $cita->update(['estado' => 'Completada']);

        return redirect()->route('citas.index')->with('success', '🏁 ¡Servicio cerrado con éxito! La ficha técnica ha sido guardada y los niveles de inventario se actualizaron correctamente.');
    }

    // ====================================================================
    // ENDPOINT DE DISPONIBILIDAD CON CAZADOR DE ERRORES INTERNOS
    // ====================================================================
    public function obtenerHorariosDisponibles(Request $request)
    {
        try {
            $request->validate([
                'fecha' => 'required|date',
                'groomer_id' => 'required|exists:users,id',
                'mascota_id' => 'required|exists:mascotas,id',
                'servicio_id' => 'required|exists:servicios,id',
            ]);

            // Carga limpia del usuario
            $groomer = \App\Models\User::find($request->groomer_id);
            $fecha = $request->fecha;

            $horaInicioEnv = '09:00';
            $horaFinEnv = '18:00';

            // Extracción ultra segura del turno soportando ambas estructuras de tablas
            $turnoRaw = $groomer->turno ?? 'completo';
            if (isset($groomer->groomer) && isset($groomer->groomer->turno)) {
                $turnoRaw = $groomer->groomer->turno;
            }
            
            $turnoNormalized = strtolower(trim($turnoRaw));

            if ($turnoNormalized === 'mañana' || $turnoNormalized === 'manana') { 
                $horaFinEnv = '13:00'; 
            }
            if ($turnoNormalized === 'tarde') { 
                $horaInicioEnv = '14:00'; 
            }

            $inicio = \Carbon\Carbon::createFromFormat('H:i', $horaInicioEnv);
            $fin = \Carbon\Carbon::createFromFormat('H:i', $horaFinEnv);

            $servicio = \App\Models\Servicio::find($request->servicio_id);
            $mascota = \App\Models\Mascota::find($request->mascota_id);
            
            // INTELIGENCIA COMPARTIDA: Lee 'duracion_base' o 'duracion_minutos' según tu migración
            $duracion = $servicio->duracion_base ?? $servicio->duracion_minutos ?? 45;

            // Soporta tanto 'tamano' como 'tamaño' con la letra Ñ
            $tamanoMascota = $mascota->tamano ?? $mascota->tamaño ?? 'Pequeño'; 

            if ($tamanoMascota === 'Mediano') { $duracion += 10; }
            elseif ($tamanoMascota === 'Grande') { $duracion += 15; }
            elseif ($tamanoMascota === 'Gigante') { $duracion += 30; }

            if ($mascota->comportamiento === 'Nervioso' || $mascota->comportamiento === 'Agresivo') { $duracion += 20; }

            $citasOcupadas = \App\Models\Cita::where('groomer_id', $groomer->id)
                ->where('fecha', $fecha)
                ->where('estado', '!=', 'Cancelada')
                ->get();

            $slotsDisponibles = [];

            while ($inicio->copy()->addMinutes($duracion)->lessThanOrEqualTo($fin)) {
                $slotInicio = $inicio->format('H:i:s');
                $slotFin = $inicio->copy()->addMinutes($duracion)->format('H:i:s');

                $cruza = false;
                foreach ($citasOcupadas as $cita) {
                    if ($slotInicio < $cita->hora_fin && $slotFin > $cita->hora_inicio) {
                        $cruza = true;
                        break;
                    }
                }

                if (!$cruza) {
                    $slotsDisponibles[] = $inicio->format('H:i');
                }

                $inicio->addMinutes(30); 
            }

            return response()->json([
                'turno' => ucfirst($turnoNormalized), 
                'horarios' => $slotsDisponibles
            ]);

        } catch (\Exception $e) {
            // Captura el fallo exacto de la base de datos y lo envía al navegador
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ====================================================================
    // MÉTODO OPTIMIZADO: CANCELAR CITA CON POLÍTICA DE 24 HORAS (Punto 4.3)
    // ====================================================================
    public function cancelar(Request $request, $id)
    {
        // 1. Validaciones estrictas: Motivo correcto y aceptación de términos obligatoria
        $request->validate([
            'motivo_cancelacion' => 'required|string|in:Salud,Tiempo,Emergencia,Otros',
            'aceptar_politica'   => 'required|accepted',
        ], [
            'motivo_cancelacion.required' => '⚠️ Por favor, seleccione un motivo válido para la cancelación.',
            'aceptar_politica.accepted'   => '⚠️ Debe aceptar los términos de la política de cancelación para continuar.',
        ]);

        $cita = \App\Models\Cita::findOrFail($id);

        // 2. Unir fecha y hora para calcular la diferencia de tiempo con Carbon
        $fechaCita = \Carbon\Carbon::parse($cita->fecha . ' ' . $cita->hora_inicio);

        // 3. REGLA DE NEGOCIO: Si el usuario es Cliente (rol_id == 4), verificar las 24 horas mínimas
        if (auth()->user()->rol_id == 4 && now()->diffInHours($fechaCita, false) < 24) {
            return back()->withErrors([
                'cancelacion_error' => '🛑 Operación rechazada: De acuerdo a nuestras políticas, los clientes solo pueden cancelar con un mínimo de 24 horas de anticipación.'
            ]);
        }

        // 4. Cambiar estado y guardar el motivo (Esto libera automáticamente el slot en obtenerHorariosDisponibles)
        $cita->update([
            'estado' => 'Cancelada',
            'motivo_cancelacion' => $request->motivo_cancelacion
        ]);

        return back()->with('success', '🚫 La cita ha sido cancelada con éxito y el espacio se encuentra libre en la agenda.');
    }
    // ====================================================================
    // EMISIÓN DE RECIBO DIGITAL DE PAGO (Punto 5.2)
    // ====================================================================
    public function generarRecibo($id)
    {
        $cita = Cita::with(['cliente', 'mascota', 'servicio', 'groomer'])->findOrFail($id);
        
        if ($cita->estado_pago !== 'Pagado') {
            return redirect()->route('citas.index')->withErrors(['error' => 'No se puede emitir un recibo de una cita que no ha sido pagada.']);
        }

        return view('citas.recibo', compact('cita'));
    }

    // ====================================================================
    // CONSOLIDACIÓN DIARIA / CIERRE DE CAJA (Punto 5.2 - SOLUCIÓN POSTGRES)
    // ====================================================================
    public function cierreCaja()
    {
        $hoy = \Carbon\Carbon::now('America/La_Paz')->format('Y-m-d');

        // 1. Recaudación de Estética (Grooming)
        $citasDelDia = Cita::where('estado_pago', 'Pagado')
            ->whereDate('fecha', $hoy)
            ->with('servicio')
            ->get();

        $totalGrooming = $citasDelDia->sum(function($cita) {
            return $cita->servicio->precio ?? 0;
        });

        // 2. Recaudación de la Tienda (Productos)
        $totalTienda = \Illuminate\Support\Facades\DB::table('citas')
            ->join('servicios', 'citas.servicio_id', '=', 'servicios.id')
            ->where('citas.estado_pago', 'Pagado')
            ->whereDate('citas.fecha', $hoy)
            ->sum('servicios.precio') - $totalGrooming;
            
        if ($totalTienda < 0) { $totalTienda = 0; }
        $totalCitas = $totalGrooming + $totalTienda;

        // 3. Agrupación por método de pago
        $consolidadoMetodos = $citasDelDia->groupBy('metodo_pago')
            ->map(function ($grupo, $metodo) {
                return (object) [
                    'metodo_pago' => $metodo,
                    'total_metodo' => $grupo->sum(function($cita) { return $cita->servicio->precio ?? 0; }),
                    'cantidad' => $grupo->count()
                ];
            });

        $totalTransacciones = $citasDelDia->count();

        // 📊 4. ALGORITMO NUEVO: Ocupación Global de la Capacidad Instalada (Módulo 12.1)
        // Contamos todas las citas agendadas activas para HOY
        $citasAgendadasHoy = Cita::whereDate('fecha', $hoy)
            ->whereIn('estado', ['Confirmada', 'Completada'])
            ->count();

        // Definimos la capacidad máxima instalada del Spa por día (Ej: 3 Groomers x 3 citas al día = 9 slots)
        $capacidadMaximaDiaria = 9; 

        // Calculamos el porcentaje matemático de uso
        $porcentajeOcupacion = $capacidadMaximaDiaria > 0 
            ? round(($citasAgendadasHoy / $capacidadMaximaDiaria) * 100, 1) 
            : 0;

        // Aseguramos que si sobrepasan la capacidad por sobre-agenda, no rompa el diseño visual (máximo 100%)
        $porcentajeBarra = $porcentajeOcupacion > 100 ? 100 : $porcentajeOcupacion;

        // 📊 5. Ranking de Rentabilidad / Top Servicios
        $rankingServicios = Cita::where('citas.estado', 'Completada')
            ->join('servicios', 'citas.servicio_id', '=', 'servicios.id')
            ->select('servicios.nombre', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total_ventas'))
            ->groupBy('servicios.nombre')
            ->orderBy('total_ventas', 'desc')
            ->take(5)
            ->get();

        return view('admin.cierre_caja', compact(
            'totalCitas', 'totalGrooming', 'totalTienda', 'consolidadoMetodos', 
            'totalTransacciones', 'hoy', 'rankingServicios', 
            'porcentajeOcupacion', 'porcentajeBarra', 'citasAgendadasHoy', 'capacidadMaximaDiaria'
        ));
    }
    // NUEVO: Vista exclusiva para confirmar citas pendientes
    public function confirmacion()
    {
        $citas = Cita::where('estado', 'Pendiente')
                      ->with(['cliente', 'mascota', 'servicio'])
                      ->orderBy('fecha', 'asc')->get();
        return view('admin.confirmacion', compact('citas'));
    }

    // NUEVO: Vista exclusiva para facturación de citas completadas
    public function facturacion()
    {
        $citas = Cita::where('estado', 'Completada')
                      ->with(['cliente', 'mascota', 'servicio'])
                      ->orderBy('fecha', 'desc')->get();
        return view('admin.facturacion', compact('citas'));
    }
    
    // 📊 Reporte Gerencial: Auditoría de Insumos Utilizados en Servicios (Punto 12.1)
    public function reporteInsumos()
    {
        // Solo el Administrador (Rol 1) tiene permiso de ver este reporte financiero-operativo
        if (auth()->user()->rol_id != 1) {
            abort(403, 'Acceso Denegado.');
        }

        // Traemos los registros de la tabla intermedia con sus relaciones fuertes
        $auditoriaInsumos = \App\Models\SalidaInsumo::with(['insumo', 'groomer', 'cita.mascota'])
            ->orderBy('fecha_salida', 'desc')
            ->get();

        return view('admin.reporte_insumos', compact('auditoriaInsumos'));
    }
    // 1. Renderiza el formulario público para que el cliente califique el servicio
    public function formularioEncuesta(Cita $cita)
    {
        // Verificamos si esta cita ya cuenta con una evaluación previa para evitar spam
        $evaluada = \App\Models\Encuesta::where('cita_id', $cita->id)->exists();
        if ($evaluada) {
            return view('welcome')->with('success', '✨ Esta cita ya ha sido calificada. ¡Muchas gracias por tu tiempo!');
        }
        return view('citas.evaluar', compact('cita'));
    }

// 2. Almacena la respuesta del cliente de forma segura en PostgreSQL
    public function guardarEncuesta(Request $request, Cita $cita)
    {
        $request->validate([
            'estrellas'  => 'required|integer|between:1,5',
            'nps'        => 'required|integer|between:0,10',
            'comentario' => 'nullable|string|max:500',
        ]);

        \App\Models\Encuesta::create([
            'cita_id'    => $cita->id,
            'estrellas'  => $request->estrellas,
            'nps'        => $request->nps,
            'comentario' => $request->comentario,
        ]);

        return redirect('/')->with('success', '🐾 ¡Muchas gracias! Tu opinión nos ayuda a mejorar el servicio para tus mascotas.');
    }

// 📊 3. Procesa y calcula las métricas gerenciales de satisfacción/NPS para el Admin
    public function reporteSatisfaccion()
    {
        if (auth()->user()->rol_id != 1) { abort(403); }

        $encuestas = \App\Models\Encuesta::with('cita.mascota')->orderBy('created_at', 'desc')->get();

        // Cálculo matemático del Promedio de Estrellas
        $promedioEstrellas = round($encuestas->avg('estrellas'), 1);

        // 📈 Cálculo oficial del Net Promoter Score (NPS)[cite: 5]
        $totalRespuestas = $encuestas->count();
        $promotores = $encuestas->where('nps', '>=', 9)->count(); // Puntuación 9 o 10[cite: 5]
        $detractores = $encuestas->where('nps', '<=', 6)->count(); // Puntuación de 0 a 6[cite: 5]

        $scoreNPS = $totalRespuestas > 0 
            ? round((($promotores - $detractores) / $totalRespuestas) * 100) 
            : 0;

        return view('admin.reporte_satisfaccion', compact('encuestas', 'promedioEstrellas', 'scoreNPS', 'totalRespuestas'));
    }
    // 1. Cronograma Diario de Citas
    public function reporteCronograma()
    {
        if (auth()->user()->rol_id != 1 && auth()->user()->rol_id != 2) { abort(403); }

        $hoy = \Carbon\Carbon::today('America/La_Paz');

        // CORRECCIÓN: Cambiamos 'hora' por 'hora_inicio' para coincidir con PostgreSQL[cite: 5]
        $citasHoy = \App\Models\Cita::whereDate('fecha', $hoy)
            ->with(['mascota', 'servicio', 'groomer'])
            ->orderBy('hora_inicio', 'asc')
            ->get();

        return view('recepcion.cronograma', compact('citasHoy'));
    }

    // 2. Reporte de Citas Canceladas o No-Show
    public function reporteCancelaciones()
    {
        if (auth()->user()->rol_id != 1 && auth()->user()->rol_id != 2) { abort(403); }

        // Filtrar citas con estados de abandono o cancelación
        $cancelaciones = \App\Models\Cita::whereIn('estado', ['Cancelada', 'No-Show'])
            ->with(['mascota', 'user'])
            ->orderBy('fecha', 'desc')
            ->get();

        return view('recepcion.cancelaciones', compact('cancelaciones'));
    }
}