<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('duracion_base'); // Aquí guardaremos los minutos (30, 60, 90)
            $table->decimal('precio', 8, 2)->default(0); // Precio en Bolivianos
            $table->text('descripcion')->nullable(); // Un texto extra por si quieres detallar qué incluye
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};