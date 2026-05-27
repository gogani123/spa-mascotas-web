<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiendaController extends Controller
{
    public function index()
    {
        $productos = DB::table('productos')->where('stock', '>', 0)->get();
        $carrito = session()->get('carrito', []);
        
        // Matemáticas del carrito
        $subtotal = 0;
        foreach($carrito as $item) {
            $subtotal += $item['precio'] * $item['cantidad'];
        }

        $descuento = session()->get('descuento', 0);
        $monto_descuento = ($subtotal * $descuento) / 100;
        $total = $subtotal - $monto_descuento;

        // Alertas de Inventario Crítico (Solo para Admin y Recepción)
        $alertasStock = collect();
        if(auth()->check() && (auth()->user()->rol_id == 1 || auth()->user()->rol_id == 2)) {
            // Busca productos donde el stock actual sea menor o igual al mínimo
            $alertasStock = DB::table('productos')->whereColumn('stock', '<=', 'stock_minimo')->get();
        }

        return view('tienda.index', compact('productos', 'carrito', 'subtotal', 'descuento', 'monto_descuento', 'total', 'alertasStock'));
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

    // NUEVO: Función para validar el cupón
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
        session()->forget('descuento'); // Borramos el descuento también
        return redirect()->back()->with('success', 'El carrito ha sido vaciado.');
    }
}