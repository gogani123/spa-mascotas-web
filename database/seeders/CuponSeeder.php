<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuponSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cupones')->insert([
            ['codigo' => 'SPA10', 'descuento_porcentaje' => 10],
            ['codigo' => 'PETLOVER20', 'descuento_porcentaje' => 20],
            ['codigo' => 'VIP50', 'descuento_porcentaje' => 50],
        ]);
    }
}