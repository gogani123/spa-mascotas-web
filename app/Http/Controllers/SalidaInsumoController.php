<?php

namespace App\Http\Controllers;

use App\Models\SalidaInsumo;
use App\Models\Insumo;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalidaInsumoController extends Controller
{
    /**
     * 7.1 - Registrar la entrega física de un insumo a un groomer (Admin o Recepción)
     */
    public function entregar(Request $request)
    {
        // Validar que el usuario sea Admin (1) o Recepción (2)
        if (Auth::user()->rol_id != 1 && Auth::user()->rol_id != 2) {
            return back()->withErrors(['error' => 'No tienes permiso para autorizar salidas de insumos.']);
        }

        $validated = $request->validate([
            'insumo_id'   => 'required|exists:insumos,id',
            'cita_id'     => 'required|exists:citas,id',
            'groomer_id'  => 'required|exists:users,id',
            'cantidad_entregada' => 'required|integer|min:1',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $insumo = Insumo::findOrFail($validated['insumo_id']);

        // Verificar si hay stock suficiente en el inventario antes de entregar
        if ($insumo->cantidad_disponible < $validated['cantidad_entregada']) {
            return back()->withErrors(['error' => "Stock insuficiente de {$insumo->nombre}. Disponible: {$insumo->cantidad_disponible}"]);
        }

        // Usamos una transacción de Base de Datos para asegurar consistencia total
        DB::transaction(function () use ($validated, $insumo) {
            // 1. Restar del inventario global
            $insumo->decrement('cantidad_disponible', $validated['cantidad_entregada']);

            // 2. Crear el registro de salida
            $salida = SalidaInsumo::create([
                'insumo_id'          => $validated['insumo_id'],
                'cita_id'            => $validated['cita_id'],
                'groomer_id'         => $validated['groomer_id'],
                'cantidad_entregada' => $validated['cantidad_entregada'],
                'cantidad_usada'     => 0,
                'cantidad_devuelta'  => 0,
                'estado'             => 'Entregado',
                'fecha_salida'       => now(),
                'observaciones'      => $validated['observaciones'],
            ]);

            // 3. Registrar en Auditoría (Logs)
            AuditLog::create([
                'usuario_id' => Auth::user()->id,
                'accion' => 'entregar_insumo_cita',
                'modelo' => 'SalidaInsumo',
                'modelo_id' => $salida->id,
                'datos_antiguos' => null,
                'datos_nuevos' => json_encode($salida),
            ]);
        });

        return back()->with('success', '✅ Insumo entregado y descontado temporalmente del inventario.');
    }

    /**
     * 7.1 - El Groomer registra lo consumido y devuelto al finalizar la atención
     */
    public function actualizarUso(Request $request, SalidaInsumo $salida)
    {
        // Validar que solo el groomer asignado o un admin puedan modificarlo
        if (Auth::user()->rol_id != 1 && Auth::user()->id != $salida->groomer_id) {
            return back()->withErrors(['error' => 'No estás autorizado para modificar los consumos de este servicio.']);
        }

        // CORREGIDO: Flexibilizamos la validación y permitimos observaciones nulas
        $validated = $request->validate([
            'cantidad_usada'    => 'required|integer|min:0',
            'cantidad_devuelta' => 'required|integer|min:0',
            'estado'            => 'required|string', 
            'observaciones'     => 'nullable|string|max:500',
        ]);

        // La suma de usado + devuelto debe cuadrar con lo que originalmente se le entregó
        $totalDeclarado = $validated['cantidad_usada'] + $validated['cantidad_devuelta'];
        if ($totalDeclarado > $salida->cantidad_entregada) {
            return back()->withErrors(['error' => "La cantidad usada y devuelta ({$totalDeclarado}) no puede ser mayor que la cantidad entregada ({$salida->cantidad_entregada})."]);
        }

        DB::transaction(function () use ($validated, $salida) {
            $insumo = $salida->insumo;
            $datosAntiguos = $salida->toArray();

            // Si el groomer devuelve insumos limpios/reutilizables, los regresamos al stock global
            if (intval($validated['cantidad_devuelta']) > 0) {
                $insumo->increment('cantidad_disponible', intval($validated['cantidad_devuelta']));
            }

            // Actualizar la salida de forma segura
            $salida->update([
                'cantidad_usada'    => $validated['cantidad_usada'],
                'cantidad_devuelta' => $validated['cantidad_devuelta'],
                'estado'            => $validated['estado'],
                'observaciones'     => $validated['observaciones'] ?? 'Uso e inventario procesado por el Groomer.',
            ]);

            // Auditoría
            AuditLog::create([
                'usuario_id' => Auth::user()->id,
                'accion' => 'completar_consumo_insumo',
                'modelo' => 'SalidaInsumo',
                'modelo_id' => $salida->id,
                'datos_antiguos' => json_encode($datosAntiguos),
                'datos_nuevos' => json_encode($salida),
            ]);
        });

        return back()->with('success', '📦 ¡Consumo de insumos actualizado e inventario sincronizado con éxito!');
    }
}