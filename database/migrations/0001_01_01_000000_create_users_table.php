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
        // 1. CREAMOS LA TABLA DE ROLES PRIMERO (Admin, Recepción, Groomer, Cliente)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); 
            $table->timestamps();
        });

        // 2. CREAMOS LA TABLA DE USUARIOS (Con los campos del PDF)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique(); // Correo único
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Contraseña encriptada
            
            // --- REQUERIMIENTOS ESPECÍFICOS DEL PDF ---
            $table->foreignId('rol_id')->nullable()->constrained('roles')->onDelete('set null'); 
            $table->boolean('estado_activo')->default(true); // Para no borrar usuarios (Inactivos)
            $table->timestamp('ultimo_acceso')->nullable(); 
            $table->string('two_factor_secret')->nullable(); // Para el 2FA del Admin
            $table->boolean('two_factor_enabled')->default(false);
            
            $table->rememberToken();
            $table->timestamps();
        });

        // 3. TABLAS POR DEFECTO DE LARAVEL PARA SEGURIDAD
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles'); // Destruimos roles al final
    }
};