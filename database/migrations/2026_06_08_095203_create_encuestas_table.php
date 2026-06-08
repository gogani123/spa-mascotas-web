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
        // CORRECCIÓN: Usamos Schema para interactuar con PostgreSQL
        \Illuminate\Support\Facades\Schema::create('encuestas', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->unique()->constrained('citas')->onDelete('cascade');
            $table->integer('estrellas'); 
            $table->integer('nps'); 
            $table->text('comentario')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
