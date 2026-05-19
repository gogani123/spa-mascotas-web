<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groomers', function (Blueprint $table) {
            $table->id();
            // Relacionamos este groomer con su cuenta de usuario
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            // Requerimientos del PDF
            $table->string('telefono')->nullable();
            $table->string('especialidad')->nullable(); // Ej: Corte fino
            $table->string('turno')->nullable(); // Ej: Mañana, Tarde
            
            // Requerimientos extras de la base de datos
            $table->integer('capacidad_simultanea')->default(1);
            $table->json('horario_trabajo')->nullable(); // Configuración JSON de horarios
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groomers');
    }
};
