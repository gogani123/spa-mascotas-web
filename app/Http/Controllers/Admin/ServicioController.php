<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    // Función para mostrar la lista de servicios en la pantalla
    public function index()
    {
        $servicios = Servicio::orderBy('nombre', 'asc')->get();
        return view('admin.servicios.index', compact('servicios'));
    }

    // Función para guardar un nuevo servicio que registre el administrador
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'duracion_base' => 'required|integer|min:15',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:500',
        ]);

        Servicio::create($request->all());

        return redirect()->route('admin.servicios.index')->with('success', '¡Nuevo servicio agregado al catálogo con éxito!');
    }

    // Función para eliminar un servicio
    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        return redirect()->route('admin.servicios.index')->with('success', 'Servicio eliminado correctamente.');
    }
}