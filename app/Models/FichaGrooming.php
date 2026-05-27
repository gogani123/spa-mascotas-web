<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaGrooming extends Model
{
    use HasFactory;

    // Le decimos a Laravel el nombre exacto de tu tabla en PostgreSQL
    protected $table = 'fichas_grooming'; 

    // Los campos que el formulario tiene permiso para rellenar
    protected $fillable = [
        'cita_id',
        'estado_ingreso',
        'temperamento',
        'observaciones',
        'recomendaciones',
        'checklist_json',
        'fotos_json',
        'foto_antes',
        'foto_despues'
    ];

    // Casts para arrays JSON
    protected $casts = [
        'checklist_json' => 'array',
        'fotos_json' => 'array',
    ];

    // Relación: Una ficha le pertenece a una Cita
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }
}