<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            
            // Relaciones: Quién pide la cita, para qué mascota, qué servicio y qué peluquero lo atiende
            $table->foreignId('cliente_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mascota_id')->constrained('mascotas')->onDelete('cascade');
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->foreignId('groomer_id')->constrained('users')->onDelete('cascade');
            
            // Tiempo exacto de la cita
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin'); // Se calculará automáticamente según lo que dure el servicio
            
            // Estado de la cita
            $table->enum('estado', ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'])->default('Pendiente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};