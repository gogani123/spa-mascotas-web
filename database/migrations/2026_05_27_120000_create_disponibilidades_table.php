<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // groomer
            $table->tinyInteger('dia_semana')->comment('0=Dom,1=Lun,...6=Sab');
            $table->boolean('abierto')->default(true);
            $table->string('hora_inicio')->nullable();
            $table->string('hora_fin')->nullable();
            $table->integer('capacidad_diaria')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('disponibilidades');
    }
};
