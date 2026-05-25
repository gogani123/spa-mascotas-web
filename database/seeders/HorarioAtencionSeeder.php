<?php

namespace Database\Seeders;

use App\Models\HorarioAtencion;
use Illuminate\Database\Seeder;

class HorarioAtencionSeeder extends Seeder
{
    public function run(): void
    {
        $dias = [
            ['numero_dia' => 1, 'nombre_dia' => 'Lunes', 'abierto' => true, 'hora_apertura' => '09:00', 'hora_cierre' => '18:00'],
            ['numero_dia' => 2, 'nombre_dia' => 'Martes', 'abierto' => true, 'hora_apertura' => '09:00', 'hora_cierre' => '18:00'],
            ['numero_dia' => 3, 'nombre_dia' => 'Miércoles', 'abierto' => true, 'hora_apertura' => '09:00', 'hora_cierre' => '18:00'],
            ['numero_dia' => 4, 'nombre_dia' => 'Jueves', 'abierto' => true, 'hora_apertura' => '09:00', 'hora_cierre' => '18:00'],
            ['numero_dia' => 5, 'nombre_dia' => 'Viernes', 'abierto' => true, 'hora_apertura' => '09:00', 'hora_cierre' => '18:00'],
            ['numero_dia' => 6, 'nombre_dia' => 'Sábado', 'abierto' => false, 'hora_apertura' => '09:00', 'hora_cierre' => '14:00'],
            ['numero_dia' => 7, 'nombre_dia' => 'Domingo', 'abierto' => false, 'hora_apertura' => '09:00', 'hora_cierre' => '14:00'],
        ];

        foreach ($dias as $dia) {
            HorarioAtencion::create($dia);
        }
    }
}