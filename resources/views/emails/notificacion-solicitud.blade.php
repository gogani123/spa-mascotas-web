<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; }
        .header { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 15px; margin-bottom: 20px; }
        .content { color: #34495e; line-height: 1.6; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ecf0f1; font-size: 12px; color: #7f8c8d; }
        .badge { display: inline-block; padding: 8px 12px; background: #f39c12; color: white; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📋 Tu solicitud de cita está en revisión</h2>
        </div>
        <div class="content">
            <p>Hola {{ $cita->cliente->name }},</p>
            <p>Tu solicitud de cita ha sido recibida exitosamente. Aquí están los detalles:</p>
            
            <ul>
                <li><strong>Mascota:</strong> {{ $cita->mascota->nombre }}</li>
                <li><strong>Servicio:</strong> {{ $cita->servicio->nombre }}</li>
                <li><strong>Fecha Solicitada:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</li>
                <li><strong>Hora Solicitada:</strong> {{ $cita->hora_inicio }}</li>
            </ul>

            <p>Nuestro equipo de recepción revisará tu solicitud y te notificará sobre la confirmación en las próximas horas.</p>
            <p><span class="badge">PENDIENTE DE CONFIRMACIÓN</span></p>
        </div>
        <div class="footer">
            <p>Spa de Mascotas - Sistema de Gestión</p>
            <p>© {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
