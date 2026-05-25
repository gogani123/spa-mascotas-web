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
        // Validamos que el cliente llene los campos obligatorios
        $request->validate([
            'nombre' => 'required|string|max:255',
            'especie' => 'required|string|max:100',
            'tamano' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'temperamento' => 'required|string',
            'carnet_vacunas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Máximo 2MB
        ]);

        // Magia para subir el archivo (Carnet)
        $rutaArchivo = null;
        if ($request->hasFile('carnet_vacunas')) {
            // Guarda el archivo en la carpeta storage/app/public/carnets
            $rutaArchivo = $request->file('carnet_vacunas')->store('carnets', 'public');
        }

        // Guardamos todo en la base de datos
        Mascota::create([
            'user_id' => Auth::id(), // El ID del dueño actual
            'nombre' => $request->nombre,
            'especie' => $request->especie,
            'raza' => $request->raza,
            'tamano' => $request->tamano,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'alergias' => $request->alergias,
            'temperamento' => $request->temperamento,
            'carnet_vacunas' => $rutaArchivo,
        ]);

        // Lo devolvemos a su lista de mascotas con un mensaje de éxito
        return redirect()->route('mascotas.index')->with('success', '¡Mascota registrada con éxito!');
    }
}