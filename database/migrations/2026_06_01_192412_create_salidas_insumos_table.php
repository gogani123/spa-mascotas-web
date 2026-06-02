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
        Schema::create('salidas_insumos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained('insumos')->onDelete('cascade');
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->foreignId('groomer_id')->constrained('users')->onDelete('cascade');
            $table->integer('cantidad_entregada');
            $table->integer('cantidad_usada')->default(0);
            $table->integer('cantidad_devuelta')->default(0);
            $table->string('estado')->default('Entregado'); // Entregado, Usado, Devuelto, Desperdiciado, Completado
            $table->timestamp('fecha_salida')->useCurrent();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas_insumos');
    }
};