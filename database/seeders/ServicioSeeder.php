<?php

namespace Database\Seeders;

use App\Models\Servicio;
use Illuminate\Database\Seeder;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        // Aquí están los datos exactos que pide tu documento 2.2
        $servicios = [
            ['nombre' => 'Baño rápido', 'duracion_base' => 30, 'precio' => 45.00, 'descripcion' => 'Lavado higiénico exprés.'],
            ['nombre' => 'Baño completo', 'duracion_base' => 60, 'precio' => 75.00, 'descripcion' => 'Lavado profundo y secado.'],
            ['nombre' => 'Corte y peinado', 'duracion_base' => 90, 'precio' => 110.00, 'descripcion' => 'Corte estético según la raza.'],
            ['nombre' => 'Servicio completo', 'duracion_base' => 120, 'precio' => 150.00, 'descripcion' => 'Baño, corte, peinado y detalles.'],
        ];

        foreach ($servicios as $servicio) {
            Servicio::create($servicio);
        }
    }
}