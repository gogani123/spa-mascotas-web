<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Mascota;
use App\Models\Bloqueo;
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

        if ($usuario->rol_id == 1 || $usuario->rol_id == 2) {
            $mascotas = Mascota::orderBy('nombre', 'asc')->get();
        } else {
            $mascotas = Mascota::where('user_id', $usuario->id)->get();
        }

        return view('citas.create', compact('servicios', 'mascotas'));
    }

    // Función que guarda la cita, calcula tiempos, valida bloqueos y TURNOS (Mañana, Tarde, Completo)
    public function store(Request $request)
    {
        $rules = [
            'mascota_id' => 'required|exists:mascotas,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
        ];

        if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2) {
            $rules['groomer_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $servicio = Servicio::findOrFail($request->servicio_id);
        $mascota = Mascota::findOrFail($request->mascota_id);
        
        // Ajuste de tiempos
        $duracion_calculada = $servicio->duracion_base;
        if ($mascota->tamano == 'Mediana') { $duracion_calculada += ($servicio->duracion_base * 0.10); } 
        elseif ($mascota->tamano == 'Grande') { $duracion_calculada += ($servicio->duracion_base * 0.15); } 
        elseif ($mascota->tamano == 'Gigante' || $mascota->tamano == 'Raza Compleja') { $duracion_calculada += ($servicio->duracion_base * 0.30); }

        if ($mascota->comportamiento == 'Nerviosa' || $mascota->comportamiento == 'Agresiva') { $duracion_calculada += 20; }

        $duracion_final = round($duracion_calculada);
        $hora_inicio = Carbon::parse($request->hora_inicio);
        $hora_fin = $hora_inicio->copy()->addMinutes($duracion_final);

        // Escudo de Bloqueos (Feriados)
        $diaBloqueado = Bloqueo::where('fecha', $request->fecha)
            ->where(function($query) use ($hora_inicio, $hora_fin) {
                $query->where('todo_el_dia', true)
                ->orWhere(function($q) use ($hora_inicio, $hora_fin) {
                    $q->where('todo_el_dia', false)
                    ->where(function($sub) use ($hora_inicio, $hora_fin) {
                        $sub->whereBetween('hora_inicio', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                            ->orWhereBetween('hora_fin', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                            ->orWhere(function($sub2) use ($hora_inicio, $hora_fin) {
                                $sub2->where('hora_inicio', '<=', $hora_inicio->format('H:i'))->where('hora_fin', '>=', $hora_fin->format('H:i'));
                            });
                    });
                });
            })->exists();

        if ($diaBloqueado) {
            return back()->withErrors(['error' => '❌ No se puede agendar: Este horario o día completo se encuentra bloqueado por la Administración.']);
        }

        // ====================================================================
        // ASIGNACIÓN Y VALIDACIÓN EXACTA DE LOS 3 TURNOS (Punto 3.1)
        // ====================================================================
        $groomer_final_id = null;
        $hora_inicio_num = (int) $hora_inicio->format('H'); // Ej: Las 08:30 se convierte en 8. Las 14:30 en 14.

        if ($request->has('groomer_id') && $request->groomer_id != null) {
            $groomer_final_id = $request->groomer_id;
            $groomer_elegido = User::find($groomer_final_id);
            $turno = $groomer_elegido->turno;

            // 1. VALIDACIÓN ESTRICTA DEL TURNO LABORAL
            // Si el turno es 'Completo', no entra en estas validaciones y pasa directo
            if ($turno == 'Mañana' && $hora_inicio_num >= 14) {
                return back()->withErrors(['error' => "❌ {$groomer_elegido->name} trabaja turno 'Mañana' (08:00 - 13:30). No puedes asignarle citas en la tarde."]);
            }
            if ($turno == 'Tarde' && $hora_inicio_num < 14) {
                return back()->withErrors(['error' => "❌ {$groomer_elegido->name} trabaja turno 'Tarde' (14:00 - 19:30). No puedes asignarle citas en la mañana."]);
            }

            // 2. VALIDAR CHOQUE DE HORARIOS
            $choque = Cita::where('groomer_id', $groomer_final_id)->where('fecha', $request->fecha)
                ->where(function($query) use ($hora_inicio, $hora_fin) {
                    $query->whereBetween('hora_inicio', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                        ->orWhereBetween('hora_fin', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                        ->orWhere(function($q) use ($hora_inicio, $hora_fin) {
                            $q->where('hora_inicio', '<=', $hora_inicio->format('H:i'))->where('hora_fin', '>=', $hora_fin->format('H:i'));
                        });
                })->exists();

            if ($choque) {
                return back()->withErrors(['error' => "❌ {$groomer_elegido->name} ya tiene una cita en esa franja horaria. Elige otra hora u otro profesional."]);
            }
        } else {
            // SI ES CLIENTE AUTOGESTIONANDO: Busca automáticamente un groomer
            $groomers = User::where('rol_id', 3)->get();
            foreach ($groomers as $groomer) {
                
                // Si la cita está fuera del turno laboral de este groomer, lo saltamos y probamos con el siguiente
                if ($groomer->turno == 'Mañana' && $hora_inicio_num >= 14) continue;
                if ($groomer->turno == 'Tarde' && $hora_inicio_num < 14) continue;
                // Si es 'Completo', simplemente no lo salta y continúa

                $choque = Cita::where('groomer_id', $groomer->id)->where('fecha', $request->fecha)
                    ->where(function($query) use ($hora_inicio, $hora_fin) {
                        $query->whereBetween('hora_inicio', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                            ->orWhereBetween('hora_fin', [$hora_inicio->format('H:i'), $hora_fin->format('H:i')])
                            ->orWhere(function($q) use ($hora_inicio, $hora_fin) {
                                $q->where('hora_inicio', '<=', $hora_inicio->format('H:i'))->where('hora_fin', '>=', $hora_fin->format('H:i'));
                            });
                    })->exists();

                if (!$choque) {
                    $groomer_final_id = $groomer->id;
                    break; 
                }
            }
        }

        if (!$groomer_final_id) {
            return back()->withErrors(['error' => 'No hay personal disponible en ese horario (Fuera de turno o con agenda llena). Elige otra hora.']);
        }

        $estado_inicial = (Auth::user()->rol_id == 4) ? 'Pendiente' : 'Confirmada';

        Cita::create([
            'cliente_id' => $mascota->user_id, 
            'mascota_id' => $request->mascota_id,
            'servicio_id' => $servicio->id,
            'groomer_id' => $groomer_final_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $hora_inicio->format('H:i'),
            'hora_fin' => $hora_fin->format('H:i'),
            'estado' => $estado_inicial,
        ]);

        return redirect()->back()->with('success', '¡Cita agendada exitosamente! El sistema verificó la disponibilidad y el turno del profesional.');
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

    // APROBACIONES (PUNTO 3.2)
    public function aprobar(Cita $cita)
    {
        if (Auth::user()->rol_id == 4) {
            return redirect()->back()->withErrors(['error' => 'Acceso denegado. Solo Recepción puede aprobar citas.']);
        }

        $cita->update(['estado' => 'Confirmada']);
        return redirect()->back()->with('success', '¡Cita de ' . $cita->mascota->nombre . ' aprobada correctamente!');
    }

    // MÓDULO DEL GROOMER (PUNTO 3.3)

    public function atender(Cita $cita)
    {
        if (Auth::user()->rol_id == 3 && $cita->groomer_id != Auth::user()->id) {
            return redirect()->route('citas.index')->withErrors(['error' => 'No tienes permiso para atender esta cita.']);
        }
        return view('citas.atender', compact('cita'));
    }

    public function completar(Request $request, Cita $cita)
    {
        $request->validate([
            'estado_inicial' => 'required|string',
            'checklist' => 'required|array',
            'foto_antes' => 'nullable|image|max:2048',
            'foto_despues' => 'nullable|image|max:2048',
        ]);

        $ruta_antes = $cita->foto_antes;
        if ($request->hasFile('foto_antes')) {
            $ruta_antes = $request->file('foto_antes')->store('fotos_citas', 'public');
        }

        $ruta_despues = $cita->foto_despues;
        if ($request->hasFile('foto_despues')) {
            $ruta_despues = $request->file('foto_despues')->store('fotos_citas', 'public');
        }

        $insumos_registrados = [
            'shampoo' => $request->input('insumo_shampoo', '0ml'),
            'perfume' => $request->input('insumo_perfume', 'No usado'),
            'algodon' => $request->input('insumo_algodon', 'No usado'),
            'observaciones' => $request->input('insumo_obs', 'Ninguna')
        ];

        $cita->update([
            'estado_inicial' => $request->estado_inicial,
            'checklist' => $request->checklist,
            'insumos' => $insumos_registrados,
            'foto_antes' => $ruta_antes,
            'foto_despues' => $ruta_despues,
            'estado' => 'Completada', 
        ]);

        return redirect()->route('citas.index')->with('success', '¡Servicio finalizado con éxito! El estado de la mascota y sus fotos quedaron registrados en su ficha técnica.');
    }
}