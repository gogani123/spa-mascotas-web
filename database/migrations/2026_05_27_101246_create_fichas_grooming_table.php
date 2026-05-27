<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichas_grooming', function (Blueprint $table) {
            $table->id();
            // Relacionamos la ficha con la cita
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            
            $table->text('estado_ingreso')->nullable();
            $table->string('temperamento')->nullable();
            
            // Campo JSONB para guardar el checklist y los insumos de forma flexible
            $table->jsonb('checklist_json')->nullable();
            
            $table->string('foto_antes')->nullable();
            $table->string('foto_despues')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichas_grooming');
    }
};