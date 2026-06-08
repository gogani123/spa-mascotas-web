<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\AuditLog;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    /**
     * Mostrar listado de insumos - Solo Administrador
     */
    public function index()
    {
        if (Auth::user()->rol_id != 1) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        $insumos = Insumo::orderBy('nombre')->paginate(15);
        $totalBajoStock = Insumo::where('cantidad_disponible', '<=', \DB::raw('cantidad_minima'))->count();

        return view('inventario.index', compact('insumos', 'totalBajoStock'));
    }

    /**
     * Mostrar formulario de crear nuevo insumo
     */
    public function create()
    {
        if (Auth::user()->rol_id != 1) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        return view('inventario.create');
    }

    /**
     * Guardar nuevo insumo
     */
    public function store(Request $request)
    {
        if (Auth::user()->rol_id != 1) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:insumos',
            'categoria' => 'required|in:Champú,Acondicionador,Herramientas,Toallas,Medicinas,Accesorios,Otros',
            'descripcion' => 'nullable|string',
            'cantidad_disponible' => 'required|integer|min:0',
            'cantidad_minima' => 'required|integer|min:1',
            'unidad' => 'required|in:Unidad,Litro,Kilogramo,Metro',
            'precio_unitario' => 'required|numeric|min:0.01',
            'proveedor' => 'nullable|string|max:255',
        ]);

        $insumo = Insumo::create($validated);

        // Registrar en auditoria
        AuditLog::create([
            'usuario_id' => Auth::user()->id,
            'accion' => 'crear_insumo',
            'modelo' => 'Insumo',
            'modelo_id' => $insumo->id,
            'datos_antiguos' => null,
            'datos_nuevos' => json_encode($validated),
        ]);

        return redirect()->route('admin.inventario.index')->with('success', "✅ Insumo '{$insumo->nombre}' creado correctamente");
    }

    /**
     * Mostrar formulario de editar insumo
     */
    public function edit(Insumo $insumo)
    {
        if (Auth::user()->rol_id != 1) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        return view('inventario.edit', compact('insumo'));
    }

    /**
     * Actualizar insumo
     */
    public function update(Request $request, Insumo $insumo)
    {
        if (Auth::user()->rol_id != 1) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:insumos,nombre,' . $insumo->id,
            'categoria' => 'required|in:Champú,Acondicionador,Herramientas,Toallas,Medicinas,Accesorios,Otros',
            'descripcion' => 'nullable|string',
            'cantidad_disponible' => 'required|integer|min:0',
            'cantidad_minima' => 'required|integer|min:1',
            'unidad' => 'required|in:Unidad,Litro,Kilogramo,Metro',
            'precio_unitario' => 'required|numeric|min:0.01',
            'proveedor' => 'nullable|string|max:255',
        ]);

        $datosAntiguos = $insumo->toArray();
        $insumo->update($validated);

        // Registrar en auditoria
        AuditLog::create([
            'usuario_id' => Auth::user()->id,
            'accion' => 'actualizar_insumo',
            'modelo' => 'Insumo',
            'modelo_id' => $insumo->id,
            'datos_antiguos' => json_encode($datosAntiguos),
            'datos_nuevos' => json_encode($validated),
        ]);

        return redirect()->route('admin.inventario.index')->with('success', "✅ Insumo '{$insumo->nombre}' actualizado correctamente");
    }

    /**
     * Eliminar insumo
     */
    public function destroy(Insumo $insumo)
    {
        if (Auth::user()->rol_id != 1) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        $nombre = $insumo->nombre;
        
        // Registrar en auditoria
        AuditLog::create([
            'usuario_id' => Auth::user()->id,
            'accion' => 'eliminar_insumo',
            'modelo' => 'Insumo',
            'modelo_id' => $insumo->id,
            'datos_antiguos' => json_encode($insumo->toArray()),
            'datos_nuevos' => null,
        ]);

        $insumo->delete();

        return redirect()->route('admin.inventario.index')->with('success', "✅ Insumo '{$nombre}' eliminado correctamente");
    }

    /**
     * Mostrar alertas de bajo stock
     */
    public function alertas()
    {
        // 1. Seguridad: Validar que sea Admin (1) o Recepción (2) según tus rutas compartidas
        if (auth()->user()->rol_id != 1 && auth()->user()->rol_id != 2) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        // 2. Tu consulta original de Bajo Stock (Insumos y productos que perforaron el mínimo)
        $insumosBajoStock = Insumo::where('cantidad_disponible', '<=', \DB::raw('cantidad_minima'))
            ->orderBy('cantidad_disponible', 'asc')
            ->paginate(20);

        $cantidadAlertas = $insumosBajoStock->total();

        // 3. NUEVO (Módulo 7.3 - Alto Consumo): Detectar si algún groomer usó más de lo normal en los últimos 7 días
        // Consideramos "Alto Consumo" si una salida individual supera las 15 unidades/mililitros
        $alertasAltoConsumo = \App\Models\SalidaInsumo::with(['insumo', 'groomer', 'cita.servicio'])
            ->where('cantidad_usada', '>=', 15)
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('cantidad_usada', 'desc')
            ->get();

        // Enviamos todo compactado respetando tus variables originales
        return view('inventario.alertas', compact('insumosBajoStock', 'cantidadAlertas', 'alertasAltoConsumo'));
    }

    /**
     * Registrar entrada de insumo (manual o de proveedor)
     */
    public function registrarEntrada(Request $request, Insumo $insumo)
    {
        if (Auth::user()->rol_id != 1 && Auth::user()->rol_id != 2) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Acceso denegado']);
        }

        $validated = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'razon' => 'nullable|string',
        ]);

        $cantidadAnterior = $insumo->cantidad_disponible;
        $insumo->cantidad_disponible += $validated['cantidad'];
        $insumo->save();

        // Registrar en auditoria
        AuditLog::create([
            'usuario_id' => Auth::user()->id,
            'accion' => 'entrada_insumo',
            'modelo' => 'Insumo',
            'modelo_id' => $insumo->id,
            'datos_antiguos' => json_encode(['cantidad' => $cantidadAnterior]),
            'datos_nuevos' => json_encode(['cantidad' => $insumo->cantidad_disponible, 'razon' => $validated['razon'] ?? '']),
        ]);

        return back()->with('success', "✅ Se agregaron {$validated['cantidad']} unidades de '{$insumo->nombre}'");
    }
    // 3. Reporte de Inventario Crítico
    public function reporteCritico()
    {
        if (auth()->user()->rol_id != 1 && auth()->user()->rol_id != 2) { abort(403); }

        // CORRECCIÓN DEFINITIVA: Usamos tus columnas reales 'cantidad_disponible' y 'cantidad_minima'
        $insumosCriticos = \App\Models\Insumo::whereRaw('cantidad_disponible <= cantidad_minima')
            ->orderBy('cantidad_disponible', 'asc')
            ->get();

        return view('recepcion.inventario_critico', compact('insumosCriticos'));
    }
}
