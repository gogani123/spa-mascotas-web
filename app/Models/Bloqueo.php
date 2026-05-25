<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloqueo extends Model
{
    use HasFactory;

    protected $table = 'bloqueos';

    protected $fillable = [
        'fecha',
        'motivo',
        'todo_el_dia',
        'hora_inicio',
        'hora_fin',
    ];
}