<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; }
        .header { color: #2c3e50; border-bottom: 3px solid #f39c12; padding-bottom: 15px; margin-bottom: 20px; }
        .content { color: #34495e; line-height: 1.6; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ecf0f1; font-size: 12px; color: #7f8c8d; }
        .alert { background: #fff3cd; padding: 15px; border-left: 4px solid #f39c12; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⏰ Recordatorio de tu cita</h2>
        </div>
        <div class="content">
            <p>Hola {{ $cita->cliente->name }},</p>
            
            @if($tipo === '24h')
                <p>Te recordamos que tu cita está programada para <strong>MAÑANA</strong>:</p>
            @else
                <p>Te recordamos que tu cita es <strong>HOY</strong>:</p>
            @endif
            
            <div class="alert">
                <p><strong>🐾 Mascota:</strong> {{ $cita->mascota->nombre }}</p>
                <p><strong>🛁 Servicio:</strong> {{ $cita->servicio->nombre }}</p>
                <p><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
                <p><strong>⏰ Hora:</strong> {{ $cita->hora_inicio }}</p>
                <p><strong>💇 Groomer:</strong> {{ $cita->groomer->name }}</p>
            </div>

            @if($tipo === '24h')
                <p>Por favor, asegúrate de llegar a tiempo mañana. Si necesitas reprogramar, contacta con nosotros lo antes posible.</p>
            @else
                <p>¡Por favor, prepárate para llegar puntualmente! Tu cita es en 2 horas.</p>
            @endif
        </div>
        <div class="footer">
            <p>Spa de Mascotas - Sistema de Gestión</p>
            <p>© {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
