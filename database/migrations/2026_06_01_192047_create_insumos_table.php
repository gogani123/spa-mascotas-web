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
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('categoria'); // Champú, Acondicionador, Toallas, etc.
            $table->text('descripcion')->nullable();
            $table->integer('cantidad_disponible')->default(0);
            $table->integer('cantidad_minima')->default(5);
            $table->string('unidad'); // Unidad, Litro, Kilogramo, etc.
            $table->decimal('precio_unitario', 8, 2);
            $table->string('proveedor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};