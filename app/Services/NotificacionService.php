<?php

namespace App\Services;

use App\Models\Notificacion;
use App\Models\Cita;
use Illuminate\Support\Facades\Mail;

class NotificacionService
{
    /**
     * Notificar cuando cliente solicita cita (estado "Pendiente")
     */
    public static function notificarSolicitudEnRevision(Cita $cita)
    {
        $cliente = $cita->cliente;

        Notificacion::create([
            'usuario_id' => $cliente->id,
            'cita_id' => $cita->id,
            'tipo' => 'solicitud',
            'asunto' => '📋 Tu solicitud de cita está en revisión',
            'mensaje' => "Tu solicitud de cita para {$cita->mascota->nombre} el {$cita->fecha} a las {$cita->hora_inicio} está siendo revisada por nuestro equipo de recepción.",
            'leida' => false,
        ]);

        // Enviar email
        try {
            Mail::send('emails.notificacion-solicitud', ['cita' => $cita], function ($message) use ($cliente) {
                $message->to($cliente->email)->subject('Tu solicitud de cita está en revisión');
            });
        } catch (\Exception $e) {
            // Log del error si falla el email
            \Log::error('Error al enviar email de solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Notificar cuando recepción confirma la cita
     */
    public static function notificarCitaConfirmada(Cita $cita)
    {
        $cliente = $cita->cliente;

        Notificacion::create([
            'usuario_id' => $cliente->id,
            'cita_id' => $cita->id,
            'tipo' => 'confirmada',
            'asunto' => '✅ Tu cita ha sido confirmada',
            'mensaje' => "¡Excelente! Tu cita para {$cita->mascota->nombre} ha sido confirmada para el {$cita->fecha} a las {$cita->hora_inicio} con {$cita->groomer->name}.",
            'leida' => false,
        ]);

        try {
            Mail::send('emails.notificacion-confirmada', ['cita' => $cita], function ($message) use ($cliente) {
                $message->to($cliente->email)->subject('✅ Tu cita ha sido confirmada');
            });
        } catch (\Exception $e) {
            \Log::error('Error al enviar email de confirmación: ' . $e->getMessage());
        }
    }

    /**
     * Notificar recordatorio 24 horas antes
     */
    public static function notificarRecordatorio24h(Cita $cita)
    {
        $cliente = $cita->cliente;

        Notificacion::create([
            'usuario_id' => $cliente->id,
            'cita_id' => $cita->id,
            'tipo' => 'recordatorio_24h',
            'asunto' => '⏰ Recordatorio: Tu cita es mañana',
            'mensaje' => "¡Recordatorio! Tu cita para {$cita->mascota->nombre} es mañana {$cita->fecha} a las {$cita->hora_inicio}. No olvides traer a tu mascota a tiempo.",
            'leida' => false,
        ]);

        try {
            Mail::send('emails.notificacion-recordatorio', 
                ['cita' => $cita, 'tipo' => '24h'], 
                function ($message) use ($cliente) {
                    $message->to($cliente->email)->subject('⏰ Recordatorio: Tu cita es mañana');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Error al enviar recordatorio 24h: ' . $e->getMessage());
        }
    }

    /**
     * Notificar recordatorio 2 horas antes
     */
    public static function notificarRecordatorio2h(Cita $cita)
    {
        $cliente = $cita->cliente;

        Notificacion::create([
            'usuario_id' => $cliente->id,
            'cita_id' => $cita->id,
            'tipo' => 'recordatorio_2h',
            'asunto' => '🚨 Tu cita es en 2 horas',
            'mensaje' => "¡Atención! Tu cita para {$cita->mascota->nombre} es HOY a las {$cita->hora_inicio}. Por favor, prepárate para llegar a tiempo.",
            'leida' => false,
        ]);

        try {
            Mail::send('emails.notificacion-recordatorio', 
                ['cita' => $cita, 'tipo' => '2h'], 
                function ($message) use ($cliente) {
                    $message->to($cliente->email)->subject('🚨 Tu cita es en 2 horas');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Error al enviar recordatorio 2h: ' . $e->getMessage());
        }
    }

    /**
     * Notificar que está listo para recoger
     */
    public static function notificarListoParaRecoger(Cita $cita)
    {
        $cliente = $cita->cliente;

        Notificacion::create([
            'usuario_id' => $cliente->id,
            'cita_id' => $cita->id,
            'tipo' => 'recojo',
            'asunto' => '🐾 ¡{$cita->mascota->nombre} está lista para recoger!',
            'mensaje' => "¡Listo! El servicio de {$cita->mascota->nombre} ha sido completado exitosamente. Puedes venir a recoger a tu mascota. Fue atendida por {$cita->groomer->name}.",
            'leida' => false,
        ]);

        try {
            Mail::send('emails.notificacion-recojo', ['cita' => $cita], function ($message) use ($cliente) {
                $message->to($cliente->email)->subject('🐾 ¡Tu mascota está lista para recoger!');
            });
        } catch (\Exception $e) {
            \Log::error('Error al enviar notificación de recojo: ' . $e->getMessage());
        }
    }

    /**
     * Notificar bajo stock de insumos (al Admin/Recepción)
     */
    public static function notificarBajoStock($insumo, $usuariosAdminRecepcion)
    {
        foreach ($usuariosAdminRecepcion as $usuario) {
            Notificacion::create([
                'usuario_id' => $usuario->id,
                'tipo' => 'bajo_stock',
                'asunto' => "⚠️ Bajo stock: {$insumo->nombre}",
                'mensaje' => "El insumo '{$insumo->nombre}' ha alcanzado el nivel mínimo de stock. Stock actual: {$insumo->cantidad_disponible} {$insumo->unidad}. Se recomienda reabastecer.",
                'leida' => false,
            ]);
        }
    }
}
