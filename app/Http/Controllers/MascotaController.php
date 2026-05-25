<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MascotaController extends Controller
{
    // 1. Mostrar la lista de mascotas del cliente
    public function index()
    {
        // Solo traemos las mascotas del usuario que inició sesión
        $mascotas = Auth::user()->mascotas; 
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
        $request->validate([
            'nombre' => 'required|string|max:255',
            'especie' => 'required|string',
            'raza' => 'nullable|string|max:255',
            'tamano' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'comportamiento' => 'required|string',
            'alergias' => 'nullable|string',
            'carnet_vacunas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $path_vacunas = null;
        if ($request->hasFile('carnet_vacunas')) {
            $path_vacunas = $request->file('carnet_vacunas')->store('carnets', 'public');
        }

        \App\Models\Mascota::create([
            'nombre' => $request->nombre,
            'especie' => $request->especie,
            'raza' => $request->raza ?? 'Mestizo',
            'tamano' => $request->tamano,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'temperamento' => $request->comportamiento, // <- CLONAMOS EL DATO AQUÍ
            'comportamiento' => $request->comportamiento,
            'alergias' => $request->alergias,
            'carnet_vacunas' => $path_vacunas,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        return redirect()->route('mascotas.index')->with('success', '¡Mascota registrada con éxito!');
    }
}