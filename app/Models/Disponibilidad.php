<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    use HasFactory;

    protected $table = 'disponibilidades';

    protected $fillable = [
        'user_id',
        'dia_semana',
        'abierto',
        'hora_inicio',
        'hora_fin',
        'capacidad_diaria',
    ];

    public function groomer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
