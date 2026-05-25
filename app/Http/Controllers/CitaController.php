<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Mascota; // <- Agregamos esto para traer a los perritos
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    // ====================================================================
    // NUEVA FUNCIÓN: Muestra el formulario en la pantalla del cliente
    // ====================================================================
    public function create()
    {
        // Traemos los servicios del catálogo que el Administrador creó
        $servicios = Servicio::orderBy('nombre', 'asc')->get();
        
        // Traemos SOLO las mascotas que le pertenecen al cliente logueado
        // (Asumimos que en tu tabla mascotas usaste 'user_id' para enlazar al dueño)
        $mascotas = Mascota::where('user_id', Auth::id())->get();

        return view('citas.create', compact('servicios', 'mascotas'));
    }

    // EL CEREBRO: Valida las Reglas del Punto 2.3 y guarda la cita
    public function store(Request $request)
    {
        $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);
        
        // Duración
        $hora_inicio = Carbon::parse($request->hora_inicio);
        $hora_fin = $hora_inicio->copy()->addMinutes($servicio->duracion_base);

        // Groomers
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

        // Horarios Ocupados
        if (!$groomer_disponible) {
            return back()->withErrors(['error' => 'No hay groomers disponibles en ese horario. Existe un solapamiento de citas. Elige otra hora.']);
        }

        // Guardar
        Cita::create([
            'cliente_id' => Auth::id(), 
            'mascota_id' => $request->mascota_id,
            'servicio_id' => $servicio->id,
            'groomer_id' => $groomer_disponible->id,
            'fecha' => $request->fecha,
            'hora_inicio' => $hora_inicio->format('H:i'),
            'hora_fin' => $hora_fin->format('H:i'),
            'estado' => 'Confirmada',
        ]);

        return redirect()->back()->with('success', '¡Cita agendada con éxito! El sistema ha evitado solapamientos y asignado un Groomer.');
    }
}