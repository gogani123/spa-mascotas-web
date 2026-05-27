<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes; // <--- AQUÍ ESTÁ LA MAGIA

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol_id',
        'google_id',
        'two_factor_secret',
        'estado',
        'telefono',
        'ci',
        'direccion',
        'especialidad',
        'turno',
        'capacidad_diaria',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Relación: Un Usuario (Dueño) tiene muchas Mascotas
     */
    public function mascotas()
    {
        return $this->hasMany(Mascota::class);
    }

    /**
     * Relación: Un Usuario tiene muchas Notificaciones
     */
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }
}