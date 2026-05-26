<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            // Ficha técnica (Estado en el que llega la mascota)
            $table->text('estado_inicial')->nullable();
            
            // Checklist de tareas realizadas (se guarda en formato JSON)
            $table->json('checklist')->nullable();
            
            // Insumos usados o desperdiciados (se guarda en formato JSON)
            $table->json('insumos')->nullable();
            
            // Fotos del antes y después (rutas de las imágenes)
            $table->string('foto_antes')->nullable();
            $table->string('foto_despues')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['estado_inicial', 'checklist', 'insumos', 'foto_antes', 'foto_despues']);
        });
    }
};