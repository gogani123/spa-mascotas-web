<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            // Revisamos si no existen para evitar errores
            if (!Schema::hasColumn('citas', 'estado_pago')) {
                $table->string('estado_pago')->default('Pendiente'); // Puede ser: Pendiente o Pagado
            }
            if (!Schema::hasColumn('citas', 'metodo_pago')) {
                $table->string('metodo_pago')->nullable(); // Puede ser: Efectivo, QR o Transferencia
            }
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['estado_pago', 'metodo_pago']);
        });
    }
};