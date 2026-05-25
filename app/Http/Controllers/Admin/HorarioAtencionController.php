<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HorarioAtencion;
use Illuminate\Http\Request;

class HorarioAtencionController extends Controller
{
    // Ver la pantalla de horarios globales
    public function index()
    {
        $horarios = HorarioAtencion::orderBy('numero_dia')->get();
        return view('admin.horarios.index', compact('horarios'));
    }

    // Guardar los cambios hechos por el Admin
    public function update(Request $request)
    {
        $datos = $request->input('horarios');

        foreach ($datos as $id => $valores) {
            $horario = HorarioAtencion::find($id);
            if ($horario) {
                $horario->update([
                    'abierto' => isset($valores['abierto']) ? true : false,
                    'hora_apertura' => $valores['hora_apertura'],
                    'hora_cierre' => $valores['hora_cierre'],
                ]);
            }
        }

        return redirect()->route('admin.horarios.index')->with('success', '¡Horario general del Spa actualizado correctamente!');
    }
}