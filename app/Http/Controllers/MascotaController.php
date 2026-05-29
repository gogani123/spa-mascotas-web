<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MascotaController extends Controller
{
    // 1. Mostrar la lista de mascotas según el rol (Corregido para Punto 4.2)
    public function index()
    {
        $usuario = Auth::user();

        // Admin(1), Recepción(2) y Groomer(3) pueden ver y buscar todas las mascotas del Spa
        if ($usuario->rol_id == 1 || $usuario->rol_id == 2 || $usuario->rol_id == 3) {
            $mascotas = Mascota::orderBy('nombre', 'asc')->get();
        } else {
            // El cliente común (Rol 4) solo ve sus propias mascotas
            $mascotas = Mascota::where('user_id', $usuario->id)->orderBy('nombre', 'asc')->get();
        }

        return view('mascotas.index', compact('mascotas'));
    }

    // 2. Mostrar el formulario para registrar una nueva mascota
    public function create()
    {
        return view('mascotas.create');
    }

    // 3. Guardar la mascota en la base de datos
    public function store(Request $request)
    {
        $rules = [
            'nombre'           => 'required|string|max:255',
            'especie'          => 'required|string',
            'raza'             => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date|before_or_equal:today', 
            'alergias'         => 'nullable|string',
            'carnet_vacunas'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tamano'           => 'required|string|in:Pequeño,Mediano,Grande,Gigante',
            'comportamiento'   => 'required|string|in:Tranquilo,Nervioso,Agresivo',
        ];

        if (auth()->user()->rol_id == 1 || auth()->user()->rol_id == 2) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        $path_vacunas = null;
        if ($request->hasFile('carnet_vacunas')) {
            $path_vacunas = $request->file('carnet_vacunas')->store('carnets', 'public');
        }

        $dueno_id = (auth()->user()->rol_id == 1 || auth()->user()->rol_id == 2) 
                    ? $request->user_id 
                    : auth()->id();

        Mascota::create([
            'nombre'           => $request->nombre,
            'especie'          => $request->especie,
            'raza'             => $request->raza ?? 'Mestizo',
            'tamano'           => $request->tamano,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'temperamento'     => $request->comportamiento, 
            'comportamiento'   => $request->comportamiento,
            'alergias'         => $request->alergias,
            'carnet_vacunas'   => $path_vacunas,
            'user_id'          => $dueno_id, 
        ]);

        return redirect()->route('mascotas.index')->with('success', '✨ ¡Mascota registrada con éxito en el perfil del cliente!');
    }

    // ====================================================================
    // NUEVO MÉTODO: PERFIL INDIVIDUAL E HISTORIAL CLÍNICO/ESTÉTICO (Punto 4.2)
    // ====================================================================
    public function show($id)
    {
        $mascota = Mascota::findOrFail($id);
        $usuario = Auth::user();

        // Control de Seguridad: Si es un cliente, bloquear si intenta ver la mascota de otro usuario
        if ($usuario->rol_id == 4 && $mascota->user_id != $usuario->id) {
            return redirect()->route('mascotas.index')->withErrors(['error' => 'Acceso denegado a este perfil.']);
        }

        // Jalamos todas las citas pasadas y actuales vinculadas estrictamente a esta mascota
        $historialCitas = Cita::where('mascota_id', $mascota->id)
            ->with(['servicio', 'groomer', 'cliente'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();

        return view('mascotas.show', compact('mascota', 'historialCitas'));
    }
    // 5. Mostrar el formulario para editar la mascota (Punto 4.2)
    public function edit($id)
    {
        $mascota = Mascota::findOrFail($id);
        $usuario = Auth::user();

        // Seguridad: Si es un cliente, impedir que intente editar un perro ajeno mediante URL
        if ($usuario->rol_id == 4 && $mascota->user_id != $usuario->id) {
            return redirect()->route('mascotas.index')->withErrors(['error' => 'No tienes autorización para modificar este perfil.']);
        }

        return view('mascotas.edit', compact('mascota'));
    }

    // 6. Guardar los cambios del formulario en la base de datos
    public function update(Request $request, $id)
    {
        $mascota = Mascota::findOrFail($id);
        
        $rules = [
            'nombre'           => 'required|string|max:255',
            'especie'          => 'required|string',
            'raza'             => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date|before_or_equal:today', 
            'alergias'         => 'nullable|string',
            'carnet_vacunas'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tamano'           => 'required|string|in:Pequeño,Mediano,Grande,Gigante',
            'comportamiento'   => 'required|string|in:Tranquilo,Nervioso,Agresivo',
        ];

        if (auth()->user()->rol_id == 1 || auth()->user()->rol_id == 2) {
            $rules['user_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        // Si sube un nuevo carnet de vacunas, reemplazar el archivo
        if ($request->hasFile('carnet_vacunas')) {
            $path_vacunas = $request->file('carnet_vacunas')->store('carnets', 'public');
            $mascota->carnet_vacunas = $path_vacunas;
        }

        $dueno_id = (auth()->user()->rol_id == 1 || auth()->user()->rol_id == 2) 
                    ? $request->user_id 
                    : auth()->id();

        // Sincronizar todos los datos modificados
        $mascota->update([
            'nombre'           => $request->nombre,
            'especie'          => $request->especie,
            'raza'             => $request->raza ?? 'Mestizo',
            'tamano'           => $request->tamano,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'temperamento'     => $request->comportamiento, 
            'comportamiento'   => $request->comportamiento,
            'alergias'         => $request->alergias,
            'user_id'          => $dueno_id,
        ]);

        return redirect()->route('mascotas.index')->with('success', '✨ Ficha técnica de la mascota actualizada correctamente en el sistema.');
    }
}