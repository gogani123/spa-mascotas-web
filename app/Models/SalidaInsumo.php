<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaInsumo extends Model
{
    use HasFactory;

    protected $table = 'salidas_insumos';

    protected $fillable = [
        'insumo_id',
        'cita_id',
        'groomer_id',
        'cantidad_entregada',
        'cantidad_usada',
        'cantidad_devuelta',
        'estado',
        'fecha_salida',
        'observaciones',
    ];

    protected $dates = [
        'fecha_salida',
        'created_at',
        'updated_at',
    ];

    /**
     * Relación: Una salida pertenece a un insumo
     */
    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    /**
     * Relación: Una salida pertenece a una cita
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    /**
     * Relación: Una salida pertenece a un groomer
     */
    public function groomer()
    {
        return $this->belongsTo(User::class, 'groomer_id');
    }

    /**
     * Obtener cantidad que falta registrar
     */
    public function getFaltantesAttribute()
    {
        $registrado = ($this->cantidad_usada ?? 0) + ($this->cantidad_devuelta ?? 0);
        return $this->cantidad_entregada - $registrado;
    }

    /**
     * Obtener porcentaje de uso
     */
    public function getPorcentajeUsoAttribute()
    {
        if ($this->cantidad_entregada == 0) return 0;
        return round((($this->cantidad_usada ?? 0) / $this->cantidad_entregada) * 100, 2);
    }
}
