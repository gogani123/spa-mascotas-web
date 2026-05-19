<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Le decimos a Laravel qué campos podemos llenar
    protected $fillable = [
        'user_id', 
        'rol_nombre', 
        'ip_address', 
        'user_agent', 
        'accion'
    ];
}