<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Mascota;
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
            $citas = Cita::with(['cliente', 'mascota', 'servicio', 'groomer'])
                         ->orderBy('fecha', 'desc')
                         ->orderBy('hora_inicio', 'desc')
                         ->get();
        } elseif ($usuario->rol_id == 3) {
            $citas = Cita::where('groomer_id', $usuario->id)
                         ->with(['cliente', 'mascota', 'servicio'])
                         ->orderBy('fecha', 'asc')
                         ->orderBy('hora_inicio', 'asc')
                         ->get();
        } else {
            $citas = Cita::where('cliente_id', $usuario->id)
                         ->with(['mascota', 'servicio', 'groomer'])
                         ->orderBy('fecha', 'asc')
                         ->orderBy('hora_inicio', 'asc')
                         ->get();
        }

        return view('citas.index', compact('citas'));
    }

    // Función para mostrar el formulario de Agendar
    public function create()
    {
        $servicios = Servicio::orderBy('nombre', 'asc')->get();
        $usuario = Auth::user();

        // LA CORRECCIÓN: Si es Admin o Recepción, traemos TODAS las mascotas
        if ($usuario->rol_id == 1 || $usuario->rol_id == 2) {
            $mascotas = Mascota::orderBy('nombre', 'asc')->get();
        } else {
            // Si es Cliente, traemos solo las suyas
            $mascotas = Mascota::where('user_id', $usuario->id)->get();
        }

        return view('citas.create', compact('servicios', 'mascotas'));
    }

    // Función que guarda la cita y calcula los tiempos
    public function store(Request $request)
    {
        $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);
        $mascota = Mascota::findOrFail($request->mascota_id);
        
        // Ajuste de tiempos (Punto 2.4)
        $duracion_calculada = $servicio->duracion_base;

        if ($mascota->tamano == 'Mediana') {
            $duracion_calculada += ($servicio->duracion_base * 0.10);
        } elseif ($mascota->tamano == 'Grande') {
            $duracion_calculada += ($servicio->duracion_base * 0.15);
        } elseif ($mascota->tamano == 'Gigante' || $mascota->tamano == 'Raza Compleja') {
            $duracion_calculada += ($servicio->duracion_base * 0.30);
        }

        if ($mascota->comportamiento == 'Nerviosa' || $mascota->comportamiento == 'Agresiva') {
            $duracion_calculada += 20; 
        }

        $duracion_final = round($duracion_calculada);
        $hora_inicio = Carbon::parse($request->hora_inicio);
        $hora_fin = $hora_inicio->copy()->addMinutes($duracion_final);

        // Validar choque de horarios (Punto 2.3)
        $groomers = User::where('rol_id', 3)->get();
        $groomer_disponible = null;

        foreach ($groomers as $groomer) {
            $choque = Cita::where('groomer_id', $groomer->id)
                ->where('fecha', $request->fecha)
                ->where(function($query) use ($hora_inicio, $hora_fin) {
                    $query->whereBetween('hora_inicio', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                        ->orWhereBetween('hora_fin', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                        ->orWhere(function($q) use ($hora_inicio, $hora_fin) {
                            $q->where('hora_inicio', '<=', $hora_inicio->format('H:i'))
                                ->where('hora_fin', '>=', $hora_fin->format('H:i'));
                        });
                })->exists();

            if (!$choque) {
                $groomer_disponible = $groomer;
                break; 
            }
        }

        if (!$groomer_disponible) {
            return back()->withErrors(['error' => 'No hay groomers disponibles en ese horario. Existe un solapamiento de citas. Elige otra hora.']);
        }

        // Si el cliente agenda (Rol 4), la cita entra como Pendiente. Si es Admin/Recepción, entra Confirmada.
        $estado_inicial = (Auth::user()->rol_id == 4) ? 'Pendiente' : 'Confirmada';
        // LA SEGUNDA CORRECCIÓN: Guardamos al dueño real (mascota->user_id), sin importar quién esté frente a la pantalla
        Cita::create([
            'cliente_id' => $mascota->user_id, 
            'mascota_id' => $request->mascota_id,
            'servicio_id' => $servicio->id,
            'groomer_id' => $groomer_disponible->id,
            'fecha' => $request->fecha,
            'hora_inicio' => $hora_inicio->format('H:i'),
            'hora_fin' => $hora_fin->format('H:i'),
            'estado' => $estado_inicial,
        ]);

        return redirect()->back()->with('success', '¡Cita agendada! El sistema calculó '. $duracion_final .' minutos en total por las características de la mascota.');
        // ====================================================================
        // PUNTO 3.1: VALIDAR BLOQUEOS DE AGENDA (Adaptado a tu tabla original)
        // ====================================================================
        $diaBloqueado = \App\Models\Bloqueo::where('fecha', $request->fecha)
            ->where(function($query) use ($hora_inicio, $hora_fin) {
                // Caso A: Es un feriado de todo el día
                $query->where('todo_el_dia', true)
                // Caso B: Es un bloqueo por horas (Mantenimiento)
                ->orWhere(function($q) use ($hora_inicio, $hora_fin) {
                    $q->where('todo_el_dia', false)
                      ->where(function($sub) use ($hora_inicio, $hora_fin) {
                          $sub->whereBetween('hora_inicio', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                              ->orWhereBetween('hora_fin', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                              ->orWhere(function($sub2) use ($hora_inicio, $hora_fin) {
                                  $sub2->where('hora_inicio', '<=', $hora_inicio->format('H:i'))
                                       ->where('hora_fin', '>=', $hora_fin->format('H:i'));
                              });
                      });
                });
            })->exists();

        if ($diaBloqueado) {
            return back()->withErrors(['error' => '❌ No se puede agendar: Este horario o día completo se encuentra bloqueado por la Administración (Feriado, Mantenimiento o Ausencia).']);
        }
        // ====================================================================
    
    }
    
    // MÓDULO DE COBRANZA (PUNTO 3.1)
    
    // 1. Muestra la pantalla del recibo o factura
    public function cobrar(Cita $cita)
    {
        return view('citas.cobrar', compact('cita'));
    }

    // 2. Guarda el pago en la base de datos
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
    // 1. Muestra la pantalla visual del calendario
    public function calendario()
    {
        return view('citas.calendario');
    }

    // 2. Envía las citas de la base de datos al calendario en formato JSON
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
                // Pintamos de verde si ya pagó, y azul si debe
                'color' => $cita->estado_pago == 'Pagado' ? '#059669' : '#4f46e5', 
            ];
        }
        
        return response()->json($eventos);
    }

    // CALENDARIO INTERACTIVO DE ARRASTRAR Y SOLTAR (PUNTO 3.1)

    // 3. Guarda la nueva fecha/hora cuando sueltas la cita con el mouse
    public function apiMover(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);
        
        // El calendario envía la fecha en formato ISO (ej: 2026-05-26T14:00:00)
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $cita->update([
            'fecha' => $start->format('Y-m-d'),
            'hora_inicio' => $start->format('H:i'),
            'hora_fin' => $end->format('H:i'),
        ]);

        return response()->json(['success' => true]);
    }
    // Función para aprobar citas pendientes (Punto 3.2)
    public function aprobar(Cita $cita)
    {
        // SEGURIDAD: Si es Cliente (Rol 4), bloqueamos la acción y lo sacamos.
        if (Auth::user()->rol_id == 4) {
            return redirect()->back()->withErrors(['error' => 'Acceso denegado. Solo Recepción puede aprobar citas.']);
        }

        $cita->update(['estado' => 'Confirmada']);
        return redirect()->back()->with('success', '¡Cita de ' . $cita->mascota->nombre . ' aprobada correctamente!');
    }
}