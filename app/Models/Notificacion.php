<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'tipo',
        'asunto',
        'mensaje',
        'leida',
        'cita_id',
    ];

    protected $casts = [
        'leida' => 'boolean',
    ];

    /**
     * Relación: Una notificación pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: Una notificación puede estar vinculada a una cita
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    /**
     * Marcar como leída
     */
    public function marcarLeida()
    {
        $this->leida = true;
        $this->save();
    }

    /**
     * Obtener notificaciones no leídas del usuario
     */
    public static function noLeidas($usuarioId)
    {
        return self::where('usuario_id', $usuarioId)
                   ->where('leida', false)
                   ->orderBy('created_at', 'desc')
                   ->get();
    }
}
