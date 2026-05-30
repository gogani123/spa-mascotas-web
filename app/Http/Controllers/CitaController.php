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

        // 3. ALGORITMO: Calcular duración exacta en base a la mascota (Punto 1 y 2 de la Rúbrica)
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
        // CORRECCIÓN DEFINITIVA: VALIDACIÓN DE SHIFT/TURNO INMUNE A CASING
        // ====================================================================
        $groomerObj = \App\Models\User::find($request->groomer_id);
        $turnoRaw = $groomerObj->turno ?? 'completo';

        // Convertimos a minúsculas y limpiamos espacios para evitar fallas de tipeo
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

        // 6. Crear la cita en la base de datos PostgreSQL
        \App\Models\Cita::create([
            'cliente_id'  => auth()->user()->rol_id == 4 ? auth()->id() : ($mascota->user_id ?? $mascota->cliente_id),
            'mascota_id'  => $request->mascota_id,
            'servicio_id' => $request->servicio_id,
            'groomer_id'  => $request->groomer_id,
            'fecha'       => $request->fecha,
            'hora_inicio' => $hora_inicio_str,
            'hora_fin'    => $hora_fin_str,
            'estado'      => auth()->user()->rol_id == 4 ? 'Pendiente' : 'Confirmada', 
            'total'       => $servicio->precio, 
        ]);

        return redirect()->route('citas.index')->with('success', '📅 ¡Cita agendada correctamente! El sistema calculó el espacio de tiempo necesario de forma automática.');
    }
    
    // MÓDULO DE COBRANZA (PUNTO 3.1)
    public function cobrar(Cita $cita)
    {
        return view('citas.cobrar', compact('cita'));
    }

    public function pagar(Request $request, Cita $cita)
    {
        $request->validate([
            'metodo_pago' => 'required|string',
        ]);

        $cita->update([
            'estado_pago' => 'Pagado',
            'metodo_pago' => $request->metodo_pago,
        ]);

        return redirect()->route('citas.index')->with('success', '¡Pago de ' . $cita->cliente->name . ' registrado correctamente por ' . $request->metodo_pago . '!');
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
        if (Auth::user()->rol_id == 4) {
            return redirect()->back()->withErrors(['error' => 'Acceso denegado. Solo Recepción puede aprobar citas.']);
        }

        $cita->update(['estado' => 'Confirmada']);
        return redirect()->back()->with('success', '¡Cita de ' . $cita->mascota->nombre . ' aprobada correctamente!');
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

        $salidaExistente = \App\Models\SalidaInsumo::where('cita_id', $citaId)
            ->where('insumo_id', $request->insumo_id)
            ->where('estado', 'Entregado')
            ->first();

        if ($salidaExistente) {
            $salidaExistente->cantidad_entregada += $request->cantidad_entregada;
            $salidaExistente->save();
        } else {
            \App\Models\SalidaInsumo::create([
                'cita_id' => $citaId,
                'insumo_id' => $request->insumo_id,
                'groomer_id' => Auth::id(),
                'cantidad_entregada' => $request->cantidad_entregada,
                'cantidad_usada' => 0,
                'cantidad_devuelta' => 0,
                'estado' => 'Entregado',
                'fecha_salida' => now(),
            ]);
        }

        return back()->with('success', '📦 Material registrado y asignado al flujo del servicio correctamente.');
    }
    public function completar(Request $request, $id)
    {
        $cita = \App\Models\Cita::findOrFail($id);

        $request->validate([
            'estado_inicial' => 'required|string',
            'foto_antes'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_despues'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $rutaAntes = null;
        if ($request->hasFile('foto_antes')) {
            $rutaAntes = $request->file('foto_antes')->store('grooming', 'public');
        }

        $rutaDespues = null;
        if ($request->hasFile('foto_despues')) {
            $rutaDespues = $request->file('foto_despues')->store('grooming', 'public');
        }

        $datosJson = [
            'tareas'  => $request->checklist ?? [],
            'insumos' => [
                'shampoo' => $request->insumo_shampoo,
                'perfume' => $request->insumo_perfume,
                'algodon' => $request->insumo_algodon,
            ]
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

        $cita->update(['estado' => 'Completada']);

        return redirect()->route('citas.index')->with('success', '¡Servicio cerrado exitosamente! La ficha técnica y las fotos han sido guardadas.');
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

        $citasDelDia = Cita::where('estado_pago', 'Pagado')
            ->whereDate('fecha', $hoy)
            ->with('servicio')
            ->get();

        $totalCitas = $citasDelDia->sum(function($cita) {
            return $cita->servicio->precio ?? 0;
        });

        // CORRECCIÓN: Usamos map para devolver objetos y que la vista no falle
        $consolidadoMetodos = $citasDelDia->groupBy('metodo_pago')
            ->map(function ($grupo, $metodo) {
                return (object) [
                    'metodo_pago' => $metodo,
                    'total_metodo' => $grupo->sum(function($cita) { return $cita->servicio->precio ?? 0; }),
                    'cantidad' => $grupo->count()
                ];
            });

        $totalTransacciones = $citasDelDia->count();

        return view('admin.cierre_caja', compact('totalCitas', 'consolidadoMetodos', 'totalTransacciones', 'hoy'));
    }
    
}