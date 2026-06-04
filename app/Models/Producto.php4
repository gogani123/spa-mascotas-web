<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria', // Alimentos, accesorios, higiene, juguetes, salud
        'variante',    // Tamaños, pesos, presentaciones o marcas
        'stock',
        'precio',
        'precio_promocional',
        'imagen_url',
    ];
}