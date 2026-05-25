<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mascotas', function (Blueprint $table) {
            // Revisamos si la columna 'tamano' NO existe, entonces la creamos
            if (!Schema::hasColumn('mascotas', 'tamano')) {
                $table->string('tamano')->default('Pequeña');
            }
            
            // Revisamos si la columna 'comportamiento' NO existe, entonces la creamos
            if (!Schema::hasColumn('mascotas', 'comportamiento')) {
                $table->string('comportamiento')->default('Normal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mascotas', function (Blueprint $table) {
            if (Schema::hasColumn('mascotas', 'tamano')) {
                $table->dropColumn('tamano');
            }
            if (Schema::hasColumn('mascotas', 'comportamiento')) {
                $table->dropColumn('comportamiento');
            }
        });
    }
};