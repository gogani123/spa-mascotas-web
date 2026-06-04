<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TiendaController extends Controller
{
    public function index(Request $request)
    {
        // 1. NUEVO: Capturar los filtros interactivos del Catálogo (Punto 8.1)
        $busqueda = $request->input('buscar');
        $categoriaSeleccionada = $request->input('categoria');

        // 2. Construir la consulta base de productos con stock disponible
        $query = DB::table('productos')->where('stock', '>', 0);

        // Aplicar filtro por texto si se escribe en el buscador
        if ($busqueda) {
            $query->where('nombre', 'LIKE', "%{$busqueda}%");
        }

        // Aplicar filtro por las Categorías oficiales del documento
        if ($categoriaSeleccionada) {
            $query->where('categoria', $categoriaSeleccionada);
        }

        // Obtener los productos paginados u ordenados
        $productos = $query->orderBy('nombre', 'asc')->get();

        // 3. Mantener intactas las Matemáticas de tu Carrito Original
        $carrito = session()->get('carrito', []);
        $subtotal = 0;
        foreach($carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }

        $descuento = session()->get('descuento', 0);
        $monto_descuento = ($subtotal * $descuento) / 100;
        $total = $subtotal - $monto_descuento;

        // 4. Alertas de Inventario Crítico (Solo para Admin y Recepción)
        $alertasStock = collect();
        if(Auth::check() && (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 2)) {
            $alertasStock = DB::table('productos')->whereColumn('stock', '<=', 'stock_minimo')->get();
        }

        // 5. NUEVO: Listado maestro de categorías para armar el menú en la vista
        $categorias = ['Alimentos', 'Accesorios', 'Higiene', 'Juguetes', 'Salud'];

        return view('tienda.index', compact(
            'productos', 
            'carrito', 
            'subtotal', 
            'descuento', 
            'monto_descuento', 
            'total', 
            'alertasStock', 
            'categorias', 
            'categoriaSeleccionada'
        ));
    }

    public function agregar(Request $request, $id)
    {
        $producto = DB::table('productos')->where('id', $id)->first();
        if(!$producto) return back()->withErrors(['error' => 'Producto no encontrado']);

        $carrito = session()->get('carrito', []);

        if(isset($carrito[$id])) {
            $carrito[$id]['cantidad']++;
        } else {
            $carrito[$id] = [
                "nombre" => $producto->nombre,
                "precio" => $producto->precio,
                "cantidad" => 1
            ];
        }

        session()->put('carrito', $carrito);
        return redirect()->back()->with('success', '¡Producto agregado al carrito!');
    }

    public function aplicarCupon(Request $request)
    {
        $cupon = DB::table('cupones')->where('codigo', strtoupper($request->codigo))->where('activo', true)->first();
        
        if(!$cupon) {
            return back()->withErrors(['error' => 'Cupón inválido, expirado o inactivo.']);
        }

        session()->put('descuento', $cupon->descuento_porcentaje);
        return back()->with('success', '¡Cupón del '.$cupon->descuento_porcentaje.'% aplicado con éxito!');
    }

    public function vaciar()
    {
        session()->forget('carrito');
        session()->forget('descuento');
        return redirect()->back()->with('success', 'El carrito ha sido vaciado.');
    }

    public function comprarPresencial(Request $request)
    {
        $request->validate([
            'metodo_pago' => 'required|string|in:Efectivo,QR,Transferencia'
        ]);

        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return back()->withErrors(['error' => 'El carrito está vacío.']);
        }

        DB::beginTransaction();
        try {
            foreach ($carrito as $id => $item) {
                $producto = DB::table('productos')->where('id', $id)->first();
                
                if (!$producto || $producto->stock < $item['cantidad']) {
                    DB::rollBack();
                    return back()->withErrors(['error' => "❌ Stock insuficiente en BD para: " . ($producto->nombre ?? 'Producto')]);
                }

                DB::table('productos')->where('id', $id)->decrement('stock', $item['cantidad']);
            }

            DB::commit();

            session()->forget('carrito');
            session()->forget('descuento');

            return redirect()->route('tienda.index')->with('success', '💵 ¡Venta registrada con éxito en el sistema! El stock de los productos ha sido actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error crítico al procesar el inventario: ' . $e->getMessage()]);
        }
    }
    // Muestra el formulario de creación (Solo Admin/Recepción)
    public function crear()
    {
        $categorias = ['Alimentos', 'Accesorios', 'Higiene', 'Juguetes', 'Salud'];
        return view('tienda.crear', compact('categorias'));
    }

    public function guardar(Request $request)
    {
        // 1. Validar los datos mínimos obligatorios del formulario
        $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|in:Alimentos,Accesorios,Higiene,Juguetes,Salud',
            'variante' => 'required|string|max:255', 
            'stock' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // 2. Unificar la variante con el nombre para cumplir el punto 8.1 de forma visual
        // Ejemplo: "Caña de pescar con un ratón" + "pequeño" -> "Caña de pescar con un ratón (pequeño)"
        $nombreCompleto = $request->nombre . ' (' . $request->variante . ')';

        // 3. Inserción ultra segura usando SOLO las columnas comerciales que sí existen en tu BD
        $idProducto = DB::table('productos')->insertGetId([
            'nombre' => $nombreCompleto,
            'categoria' => $request->categoria,
            'stock' => $request->stock,
            'precio' => $request->precio,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // 4. Guardar la foto físicamente en una carpeta independiente usando el ID numérico
        if ($request->hasFile('imagen')) {
            $extension = $request->file('imagen')->getClientOriginalExtension();
            $nombreArchivo = $idProducto . '.' . $extension; // Ejemplo: 12.jpg, 13.png
            
            // Creamos una carpeta pública directa para evitar enredos de permisos
            $request->file('imagen')->move(public_path('fotos_catalogo'), $nombreArchivo);
        }

        return redirect()->route('tienda.index')->with('success', '📦 ¡Nuevo producto registrado correctamente con su fotografía!');
    }
}