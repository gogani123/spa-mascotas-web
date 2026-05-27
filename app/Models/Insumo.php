<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $table = 'insumos';

    protected $fillable = [
        'nombre',
        'categoria',
        'descripcion',
        'cantidad_disponible',
        'cantidad_minima',
        'unidad',
        'precio_unitario',
        'proveedor',
    ];

    // Relación: Un insumo tiene muchas salidas
    public function salidaInsumos()
    {
        return $this->hasMany(SalidaInsumo::class, 'insumo_id');
    }

    /**
     * Verificar si está en nivel bajo de stock
     */
    public function estaBajoStock()
    {
        return $this->cantidad_disponible <= $this->cantidad_minima;
    }

    /**
     * Obtener cantidad disponible formateada
     */
    public function getEstadoStockAttribute()
    {
        if ($this->estaBajoStock()) {
            return 'crítico';
        }
        return 'disponible';
    }
}
