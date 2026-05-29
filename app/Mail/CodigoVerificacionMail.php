<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodigoVerificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $codigo;
    public $usuarioNombre;

    public function __construct($codigo, $usuarioNombre)
    {
        $this->codigo = $codigo;
        $this->usuarioNombre = $usuarioNombre;
    }

    public function build()
    {
        return $this->subject('🔑 Tu Código de Verificación - Spa Mascotas')
                    ->html("
                        <div style='font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #374151; background-color: #1f2937; color: #f3f4f6; rounded-md;'>
                            <h2 style='color: #818cf8; text-align: center;'>¡Hola, {$this->usuarioNombre}!</h2>
                            <p style='font-size: 16px; text-align: center;'>Gracias por registrarte en nuestro Spa de Mascotas. Para activar tu cuenta, ingresa el siguiente código de verificación en tu pantalla de registro:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <span style='background-color: #111827; border: 2px dashed #4f46e5; color: #ffffff; font-size: 32px; font-weight: bold; letter-spacing: 5px; padding: 15px 30px; border-radius: 8px; display: inline-block;'>
                                    {$this->codigo}
                                </span>
                            </div>
                            <p style='font-size: 12px; color: #9ca3af; text-align: center;'>Este código es de uso único y confidencial. Si no solicitaste este registro, puedes ignorar este correo.</p>
                        </div>
                    ");
    }
}