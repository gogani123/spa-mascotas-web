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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            // 1. ¿Quién? (ID de usuario)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); 
            $table->string('rol_nombre')->nullable(); 
            
            // 3. ¿Desde dónde? (Dirección IP y navegador)
            $table->string('ip_address')->nullable(); 
            $table->text('user_agent')->nullable(); 
            
            // 4. ¿Qué hizo? (Ej: "Intento de inicio de sesión")
            $table->string('accion'); 
            
            // 2. ¿Cuándo? (Esto crea la fecha y hora exacta automáticamente)
            $table->timestamps(); 
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
