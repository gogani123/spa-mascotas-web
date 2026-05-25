<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bloqueo;
use Illuminate\Http\Request;

class BloqueoController extends Controller
{
    // Ver la pantalla principal de bloqueos
    public function index()
    {
        // Traemos los bloqueos ordenados por fecha futura
        $bloqueos = Bloqueo::orderBy('fecha', 'asc')->get();
        return view('admin.bloqueos.index', compact('bloqueos'));
    }

    // Guardar un nuevo bloqueo de agenda
    public function store(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'motivo' => 'required|string|max:255',
            'todo_el_dia' => 'nullable|boolean',
            'hora_inicio' => 'required_if:todo_el_dia,0',
            'hora_fin' => 'required_if:todo_el_dia,0',
        ]);

        Bloqueo::create([
            'fecha' => $request->fecha,
            'motivo' => $request->motivo,
            'todo_el_dia' => $request->has('todo_el_dia') ? true : false,
            'hora_inicio' => $request->has('todo_el_dia') ? null : $request->hora_inicio,
            'hora_fin' => $request->has('todo_el_dia') ? null : $request->hora_fin,
        ]);

        return redirect()->route('admin.bloqueos.index')->with('success', '¡Agenda bloqueada correctamente para la fecha seleccionada!');
    }

    // Eliminar o levantar un bloqueo
    public function destroy($id)
    {
        $bloqueo = Bloqueo::findOrFail($id);
        $bloqueo->delete();

        return redirect()->route('admin.bloqueos.index')->with('success', '¡El bloqueo ha sido levantado. El día vuelve a estar disponible!');
    }
}