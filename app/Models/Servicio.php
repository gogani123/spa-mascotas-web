<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    // Habilitamos los campos para que Laravel nos deje guardar la información
    protected $fillable = [
        'nombre',
        'duracion_base',
        'precio',
        'descripcion',
    ];
}