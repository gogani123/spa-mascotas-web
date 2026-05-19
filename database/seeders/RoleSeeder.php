<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin', 'Recepción', 'Groomer', 'Cliente'];

        foreach ($roles as $rol) {
            \Illuminate\Support\Facades\DB::table('roles')->insert([
                'nombre' => $rol,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
