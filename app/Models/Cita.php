<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    // Habilitamos los campos que se pueden guardar
    protected $fillable = [
        'cliente_id',
        'mascota_id',
        'servicio_id',
        'groomer_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
    ];

    // ==========================================================
    // RELACIONES: Esto permite que el sistema sepa quién es quién
    // ==========================================================
    
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function mascota()
    {
        return $this->belongsTo(Mascota::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    public function groomer()
    {
        return $this->belongsTo(User::class, 'groomer_id');
    }
}