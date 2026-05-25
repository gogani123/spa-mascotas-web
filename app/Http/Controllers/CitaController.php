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
    // Muestra el formulario en la pantalla del cliente
    public function create()
    {
        $servicios = Servicio::orderBy('nombre', 'asc')->get();
        $mascotas = Mascota::where('user_id', Auth::id())->get();

        return view('citas.create', compact('servicios', 'mascotas'));
    }

    // EL CEREBRO: Valida las Reglas del Punto 2.3 y el Punto 2.4
    public function store(Request $request)
    {
        $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'servicio_id' => 'required|exists:servicios,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
        ]);

        $servicio = Servicio::findOrFail($request->servicio_id);
        $mascota = Mascota::findOrFail($request->mascota_id); // Traemos los datos del perrito
        
        // ====================================================================
        // PUNTO 2.4: AJUSTE AUTOMÁTICO DE DURACIÓN SEGÚN LA MASCOTA
        // ====================================================================
        $duracion_calculada = $servicio->duracion_base;

        // Regla: Ajuste por tamaño (Porcentajes)
        if ($mascota->tamano == 'Mediana') {
            $duracion_calculada += ($servicio->duracion_base * 0.10); // + 10%
        } elseif ($mascota->tamano == 'Grande') {
            $duracion_calculada += ($servicio->duracion_base * 0.15); // + 15%
        } elseif ($mascota->tamano == 'Gigante' || $mascota->tamano == 'Raza Compleja') {
            $duracion_calculada += ($servicio->duracion_base * 0.30); // + 30%
        }

        // Regla: Mascotas nerviosas o agresivas (Criterio técnico: + 20 minutos fijos)
        if ($mascota->comportamiento == 'Nerviosa' || $mascota->comportamiento == 'Agresiva') {
            $duracion_calculada += 20; 
        }

        // Redondeamos para que no existan minutos decimales
        $duracion_final = round($duracion_calculada);

        // ====================================================================
        // Calculamos la hora exacta de fin usando la nueva duración
        // ====================================================================
        $hora_inicio = Carbon::parse($request->hora_inicio);
        $hora_fin = $hora_inicio->copy()->addMinutes($duracion_final);

        // PUNTO 2.3: Validamos si hay groomers disponibles (Solapamiento)
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

        // Guardamos la cita con la hora de finalización YA MODIFICADA por el tamaño
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

        return redirect()->back()->with('success', '¡Cita agendada! El sistema calculó '. $duracion_final .' minutos en total por las características de tu mascota.');
    }
}