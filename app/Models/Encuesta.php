<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encuesta extends Model
{
    protected $fillable = ['cita_id', 'estrellas', 'nps', 'comentario'];

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class);
    }
}