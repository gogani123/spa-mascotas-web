<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioAtencion extends Model
{
    use HasFactory;

    protected $table = 'horarios_atencion';

    protected $fillable = [
        'numero_dia',
        'nombre_dia',
        'abierto',
        'hora_apertura',
        'hora_cierre',
    ];
}