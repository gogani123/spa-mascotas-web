<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            // Conexión con el cliente (Dueño)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Datos de la mascota según requerimientos
            $table->string('nombre');
            $table->string('especie'); // Perro, gato u otro
            $table->string('raza')->nullable(); 
            $table->string('tamano'); // Pequeño, mediano, grande o gigante
            $table->date('fecha_nacimiento');
            $table->text('alergias')->nullable();
            $table->string('temperamento'); // Tranquilo, nervioso, agresivo, inquieto
            $table->string('carnet_vacunas')->nullable(); // Aquí guardaremos la ruta del PDF/Imagen
            
            // Seguridad y control
            $table->softDeletes(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mascotas');
    }
};