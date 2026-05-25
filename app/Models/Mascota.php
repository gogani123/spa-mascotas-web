<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mascota extends Model
{
    use HasFactory, SoftDeletes;

    // Campos que permitimos llenar desde un formulario
    protected $fillable = [
        'nombre',
        'especie',
        'raza',
        'fecha_nacimiento', 
        'temperamento',     // <- LO DEVOLVEMOS PARA QUE LA BASE DE DATOS NO SE ENOJE
        'alergias',
        'carnet_vacunas',
        'user_id',
        'tamano',           
        'comportamiento',   
    ];

    // Relación: Una mascota pertenece a un dueño (Usuario)
    public function dueno()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}