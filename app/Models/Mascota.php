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
        'user_id',
        'nombre',
        'especie',
        'raza',
        'tamano',
        'fecha_nacimiento',
        'alergias',
        'temperamento',
        'carnet_vacunas',
    ];

    // Relación: Una mascota pertenece a un dueño (Usuario)
    public function dueno()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}