<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregamos el límite de alerta a los productos existentes
        Schema::table('productos', function (Blueprint $table) {
            $table->integer('stock_minimo')->default(5)->after('stock');
        });

        // 2. Creamos la tabla de Cupones
        Schema::create('cupones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->integer('descuento_porcentaje');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupones');
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('stock_minimo');
        });
    }
};