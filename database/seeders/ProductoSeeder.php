<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['nombre' => 'Croquetas Premium DogChow 3kg', 'categoria' => 'Alimentos', 'precio' => 120.00, 'stock' => 15],
            ['nombre' => 'Shampoo Fragancia Lavanda 500ml', 'categoria' => 'Higiene', 'precio' => 45.50, 'stock' => 20],
            ['nombre' => 'Hueso de Goma Masticable', 'categoria' => 'Juguetes', 'precio' => 25.00, 'stock' => 30],
            ['nombre' => 'Collar Reflectante Talla M', 'categoria' => 'Accesorios', 'precio' => 35.00, 'stock' => 10],
            ['nombre' => 'Arena para Gatos 5kg', 'categoria' => 'Higiene', 'precio' => 55.00, 'stock' => 25],
        ];

        foreach ($productos as $producto) {
            // Usamos DB::table por si no tienes el Modelo Producto creado aún
            DB::table('productos')->insert($producto);
        }
    }
}