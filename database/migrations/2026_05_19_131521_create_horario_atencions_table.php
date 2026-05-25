<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_atencion', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_dia'); // 1 = Lunes, 2 = Martes... 7 = Domingo
            $table->string('nombre_dia');  // 'Lunes', 'Martes', etc.
            $table->boolean('abierto')->default(true); // true = abierto, false = cerrado
            $table->time('hora_apertura')->default('09:00:00');
            $table->time('hora_cierre')->default('18:00:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_atencion');
    }
};