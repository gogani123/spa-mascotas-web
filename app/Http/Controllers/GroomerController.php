<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\FichaGrooming;
use App\Models\Mascota;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Insumo;
use App\Models\SalidaInsumo;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroomerController extends Controller
{
    /**
     * Mostrar la agenda personal del groomer (hoy o filtrada)
     */
    public function agendaPersonal(Request $request)
    {
        $groomer = Auth::user();

        // Validar que sea groomer (rol_id = 3)
        if ($groomer->rol_id != 3) {
            abort(403, 'Acceso denegado. Solo groomers pueden acceder aquí.');
        }

        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        $vista = $request->get('vista', 'dia'); // 'dia' o 'semana'

        if ($vista === 'semana') {
            $fechaInicio = Carbon::parse($fecha)->startOfWeek();
            $fechaFin = Carbon::parse($fecha)->endOfWeek();
            $citas = Cita::where('groomer_id', $groomer->id)
                        ->whereBetween('fecha', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
                        ->with(['mascota', 'servicio', 'cliente'])
                        ->orderBy('fecha', 'asc')
                        ->orderBy('hora_inicio', 'asc')
                        ->get();
        } else {
            // Vista diaria
            $citas = Cita::where('groomer_id', $groomer->id)
                        ->where('fecha', $fecha)
                        ->with(['mascota', 'servicio', 'cliente'])
                        ->orderBy('hora_inicio', 'asc')
                        ->get();
        }

        return view('groomer.agenda', compact('citas', 'fecha', 'vista', 'groomer'));
    }

    /**
     * Mostrar ficha técnica de una cita
     */
    public function fichaPanel($citaId)
    {
        $cita = Cita::findOrFail($citaId);
        $groomer = Auth::user();

        // Validar permisos de seguridad operativa
        if ($groomer->rol_id != 3 || $cita->groomer_id != $groomer->id) {
            abort(403, 'Acceso denegado.');
        }

        // REGLA DE NEGOCIO: Al abrir la ficha, la cita pasa automáticamente a "En Progreso"
        if ($cita->estado === 'Confirmada') {
            $cita->update(['estado' => 'En Progreso']);
        }

        $ficha = $cita->fichaGrooming ?? new FichaGrooming();
        $mascota = $cita->mascota;
        $servicio = $cita->servicio;

        // Definir checklist base según servicio
        $checklistBase = $this->getChecklistBase($servicio->id);

        // Insumos asignados para esta cita
        $insumos = SalidaInsumo::where('cita_id', $citaId)
                                ->with('insumo')
                                ->get();

        // Fotos cargadas
        $fotos = $ficha->fotos_json ? json_decode($ficha->fotos_json, true) : [];

        return view('groomer.ficha', compact('cita', 'ficha', 'mascota', 'servicio', 'checklistBase', 'insumos', 'fotos'));
    }

    /**
     * Guardar o actualizar ficha técnica básica
     */
    public function guardarFicha(Request $request, $citaId)
    {
        $cita = Cita::findOrFail($citaId);
        $groomer = Auth::user();

        if ($groomer->rol_id != 3 || $cita->groomer_id != $groomer->id) {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'estado_ingreso' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
            'temperamento' => 'required|string|in:tranquilo,nervioso,agresivo,inquieto',
        ]);

        $ficha = $cita->fichaGrooming ?? new FichaGrooming();
        $ficha->cita_id = $citaId;
        $ficha->estado_ingreso = $validated['estado_ingreso'];
        $ficha->observaciones = $validated['observaciones'] ?? null;
        $ficha->temperamento = $validated['temperamento'];
        $ficha->save();

        return back()->with('success', '✅ Ficha técnica guardada correctamente.');
    }

    /**
     * Registrar checklist de tareas (BUG CORREGIDO)
     */
    public function guardarChecklist(Request $request, $citaId)
    {
        $cita = Cita::findOrFail($citaId);
        $groomer = Auth::user();

        if ($groomer->rol_id != 3 || $cita->groomer_id != $groomer->id) {
            abort(403, 'Acceso denegado.');
        }

        // Capturamos el array de elementos marcados enviado por el formulario
        $checklist = $request->get('checklist', []);

        // CORRECCIÓN DEL BUG: Contamos cuántas casillas fueron enviadas como marcadas
        $completados = count($checklist);
        
        if ($completados < 5) {
            return back()->withErrors(['error' => '❌ Módulo Grooming: Debes marcar al menos 3 ítems del checklist para registrar el avance técnico.']);
        }

        $ficha = $cita->fichaGrooming ?? new FichaGrooming();
        $ficha->cita_id = $citaId;
        $ficha->checklist_json = json_encode($checklist);
        $ficha->save();

        return back()->with('success', '✅ Checklist registrado correctamente.');
    }

    /**
     * Cargar fotos (antes y después)
     */
    public function cargarFotos(Request $request, $citaId)
    {
        $cita = Cita::findOrFail($citaId);
        $groomer = Auth::user();

        if ($groomer->rol_id != 3 || $cita->groomer_id != $groomer->id) {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'fotos' => 'required|array|min:1|max:10',
            'fotos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', 
            'tipo_foto' => 'required|in:antes,durante,despues',
        ]);

        $ficha = $cita->fichaGrooming ?? new FichaGrooming();
        $ficha->cita_id = $citaId;

        $fotosExistentes = $ficha->fotos_json ? json_decode($ficha->fotos_json, true) : [];

        foreach ($request->file('fotos') as $foto) {
            $path = $foto->store("citas/{$citaId}", 'public');
            $fotosExistentes[] = [
                'tipo' => $validated['tipo_foto'],
                'path' => $path,
                'fecha' => now()->toDateTimeString(),
            ];
        }

        $ficha->fotos_json = json_encode($fotosExistentes);
        $ficha->save();

        return back()->with('success', '✅ Evidencia fotográfica cargada correctamente.');
    }

    /**
     * Panel de insumos para una cita
     */
    public function panelInsumos($citaId)
    {
        $cita = Cita::findOrFail($citaId);
        $groomer = Auth::user();

        if ($groomer->rol_id != 3 || $cita->groomer_id != $groomer->id) {
            abort(403, 'Acceso denegado.');
        }

        $insumos = SalidaInsumo::where('cita_id', $citaId)
                                ->with('insumo')
                                ->get();

        return view('groomer.insumos', compact('cita', 'insumos'));
    }

    /**
     * Registrar uso de insumos
     */
    public function registrarUsoInsumos(Request $request, $citaId)
    {
        $cita = Cita::findOrFail($citaId);
        $groomer = Auth::user();

        if ($groomer->rol_id != 3 || $cita->groomer_id != $groomer->id) {
            abort(403, 'Acceso denegado.');
        }

        $validated = $request->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad_usada' => 'required|numeric|min:0',
            'cantidad_devuelta' => 'required|numeric|min:0',
            'estado' => 'required|in:usado,devuelto,desperdiciado',
        ]);

        $salidaInsumo = SalidaInsumo::where('cita_id', $citaId)
                                    ->where('insumo_id', $validated['insumo_id'])
                                    ->first();

        if ($salidaInsumo) {
            $salidaInsumo->cantidad_usada = $validated['cantidad_usada'];
            $salidaInsumo->cantidad_devuelta = $validated['cantidad_devuelta'];
            $salidaInsumo->estado = $validated['estado'];
            $salidaInsumo->save();
        }

        return back()->with('success', '✅ Uso de insumo registrado.');
    }

    /**
     * Cerrar servicio (CON VERIFICACIÓN AUTOMÁTICA DE BAJO STOCK - PUNTO 9)
     */
    public function cerrarServicio(Request $request, $citaId)
    {
        $cita = Cita::findOrFail($citaId);
        $groomer = Auth::user();

        if ($groomer->rol_id != 3 || $cita->groomer_id != $groomer->id) {
            abort(403, 'Acceso denegado.');
        }

        $ficha = $cita->fichaGrooming;

        // Regla de Negocio: Validar checklist antes de cerrar la hoja
        if (!$ficha || !$ficha->checklist_json) {
            return back()->withErrors(['error' => '❌ Restricción: No se puede cerrar la ficha sin completar el checklist técnico.']);
        }

        // =========================================================================
        // AUTOMATIZACIÓN DE FLUJO DE CUMPLIMIENTO Y CAJA ELECTRÓNICA
        // =========================================================================
        $cita->estado = 'Completada'; 
        $cita->estado_pago = 'Pagado'; 
        $cita->save();

        // 1. Descontar automáticamente insumos del inventario global
        $salidaInsumos = SalidaInsumo::where('cita_id', $citaId)->get();
        
        // Capturar de forma preventiva a los usuarios que deben recibir alertas de almacén
        $usuariosAlerta = \App\Models\User::whereIn('rol_id', [1, 2])->get();

        foreach ($salidaInsumos as $salida) {
            if ($salida->estado === 'usado' || $salida->estado === 'desperdiciado') {
                $insumo = $salida->insumo;
                if ($insumo) {
                    $insumo->cantidad_disponible -= $salida->cantidad_usada;
                    $insumo->save();

                    // ====================================================================
                    // ⚡ NUEVO DISPARADOR AUTOMÁTICO: DETECTOR DE BAJO STOCK (PUNTO 9)
                    // ====================================================================
                    // Si tras el consumo en cabina el stock iguala o cae por debajo del mínimo, gatillar la alerta
                    if ($insumo->cantidad_disponible <= $insumo->cantidad_minima) {
                        \App\Services\NotificacionService::notificarBajoStock($insumo, $usuariosAlerta);
                    }
                    // ====================================================================
                }
            }
        }

        // 2. Disparar evento de notificación de recojo al cliente en tiempo real
        NotificacionService::notificarListoParaRecoger($cita);

        return redirect('/citas')->with('success', '✨ ¡Servicio finalizado con éxito! La mascota está lista para recojo, el inventario fue actualizado y se evaluaron las alertas de stock.');    
    }

    /**
     * Obtener checklist base según el tipo de servicio
     */
    private function getChecklistBase($servicioId)
    {
        return [
            'uñas_cortadas' => false,
            'oidos_limpios' => false,
            'glandulas_anales' => false,
            'baño_completo' => false,
            'secado_completo' => false,
            'perfume_aplicado' => false,
            'inspección_de_piel' => false,
            'recomendaciones_dadas' => false,
        ];
    }
    public function registrarSalida(Request $request, $citaId)
{
    // Validamos datos básicos
    $request->validate([
        'insumo_id' => 'required|exists:insumos,id',
        'cantidad_entregada' => 'required|integer|min:1',
    ]);

    // Creamos el registro de salida (Punto 7.1 del documento)
    \App\Models\SalidaInsumo::create([
        'cita_id' => $citaId,
        'insumo_id' => $request->insumo_id,
        'groomer_id' => auth()->id(), // Groomer responsable
        'cantidad_entregada' => $request->cantidad_entregada,
        'estado' => 'Entregado', // Estado inicial según documento
        'fecha_salida' => now(),
    ]);

    return back()->with('success', 'Insumo registrado con éxito.');
}
}