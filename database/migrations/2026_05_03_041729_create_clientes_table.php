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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            // Relacionamos este cliente con su cuenta de usuario (Email y Password)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            // Requerimientos del PDF
            $table->string('ci')->nullable(); 
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            
            // Requerimientos extras de la base de datos
            $table->text('preferencias')->nullable(); // Canal de notificación preferido
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
