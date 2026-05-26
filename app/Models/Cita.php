<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'mascota_id',
        'servicio_id',
        'groomer_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'estado_pago',
        'metodo_pago',
    ];

    // Relación: Una cita pertenece a un Cliente (Usuario)
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // Relación: Una cita pertenece a una Mascota
    public function mascota()
    {
        return $this->belongsTo(Mascota::class, 'mascota_id');
    }

    // Relación: Una cita incluye un Servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    // Relación: Una cita es atendida por un Groomer (Usuario)
    public function groomer()
    {
        return $this->belongsTo(User::class, 'groomer_id');
    }
}