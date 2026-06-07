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
     * Notificar que la mascota está lista para recoger (PUNTO 9 - INTERFAZ GROOMER)
     */
    public static function notificarListoParaRecoger(Cita $cita)
    {
        $cliente = $cita->cliente;

        // CORRECCIÓN DEL BUG: Usamos comillas dobles "" para permitir la interpolación de variables en PHP
        Notificacion::create([
            'usuario_id' => $cliente->id,
            'cita_id'    => $cita->id,
            'tipo'       => 'recojo',
            'asunto'     => "🐾 ¡{$cita->mascota->nombre} está lista para recoger!", // 👈 ¡Luz verde aquí!
            'mensaje'    => "¡Listo! El servicio de {$cita->mascota->nombre} ha sido completado exitosamente. Puedes venir a recoger a tu mascota. Fue atendida por {$cita->groomer->name}.",
            'leida'      => false,
        ]);

        // Envío asíncrono del correo electrónico usando la plantilla de recojo
        try {
            Mail::send('emails.notificacion-recojo', ['cita' => $cita], function ($message) use ($cliente) {
                $message->to($cliente->email)->subject('🐾 ¡Tu mascota está lista para recoger!');
            });
        } catch (\Exception $e) {
            // Protección del hilo del servidor en caso de desconexión del servicio SMTP
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
    /**
     * Notificar Pago Registrado (PUNTO 9 - Citas y Tienda)
     */
    public static function notificarPagoRegistrado($usuarioId, $monto, $concepto, $metodoPago)
    {
        // 1. Alerta para el Cliente (o el usuario que pagó)
        $notificacionCliente = Notificacion::create([
            'usuario_id' => $usuarioId,
            'tipo'       => 'pago_registrado',
            'asunto'     => "💵 ¡Pago Recibido con Éxito!",
            'mensaje'    => "Hemos registrado tu pago de Bs. " . number_format($monto, 2) . " mediante {$metodoPago} por el concepto de: {$concepto}. ¡Gracias por tu preferencia! 😊🐾",
            'leida'      => false,
        ]);

        // 2. Alerta para el personal de Recepción y Administración (Rol 1 y 2)
        $personalCaja = \App\Models\User::whereIn('rol_id', [1, 2])->get();
        foreach ($personalCaja as $empleado) {
            Notificacion::create([
                'usuario_id' => $empleado->id,
                'tipo'       => 'pago_recepcion',
                'asunto'     => "💰 Nueva Entrada en Caja: Bs. " . number_format($monto, 2),
                'mensaje'    => "Se ha procesado un cobro de Bs. " . number_format($monto, 2) . " ({$metodoPago}) por el concepto: {$concepto}.",
                'leida'      => false,
            ]);
        }

        // 3. Opcional: Enviar el correo electrónico de respaldo al cliente si tiene un email activo
        try {
            $cliente = \App\Models\User::find($usuarioId);
            if ($cliente && $cliente->email) {
                Mail::raw("Hola {$cliente->name}, te confirmamos que hemos recibido tu pago de Bs. " . number_format($monto, 2) . " por concepto de: {$concepto} vía {$metodoPago}.", function ($message) use ($cliente) {
                    $message->to($cliente->email)->subject('💵 Comprobante de Pago Recibido - Spa Mascotas');
                });
            }
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo de pago registrado: ' . $e->getMessage());
        }
    }
}
