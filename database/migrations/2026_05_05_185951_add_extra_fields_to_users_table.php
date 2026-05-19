<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Campo de Estado (Común) - Por defecto estará "Activo" (true)
            $table->boolean('estado')->default(true)->after('rol_id');

            // Campos para Clientes y Personal (pueden estar vacíos al principio)
            $table->string('telefono')->nullable()->after('estado');
            $table->string('ci')->nullable()->after('telefono');
            $table->string('direccion')->nullable()->after('ci');
            
            // Campos exclusivos del Personal
            $table->string('especialidad')->nullable()->after('direccion');
            $table->string('turno')->nullable()->after('especialidad');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['estado', 'telefono', 'ci', 'direccion', 'especialidad', 'turno']);
        });
    }
};