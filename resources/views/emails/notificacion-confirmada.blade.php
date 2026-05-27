<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; }
        .header { color: #2c3e50; border-bottom: 3px solid #27ae60; padding-bottom: 15px; margin-bottom: 20px; }
        .content { color: #34495e; line-height: 1.6; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ecf0f1; font-size: 12px; color: #7f8c8d; }
        .badge { display: inline-block; padding: 8px 12px; background: #27ae60; color: white; border-radius: 4px; }
        .info-box { background: #ecf0f1; padding: 15px; border-left: 4px solid #27ae60; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✅ Tu cita ha sido confirmada</h2>
        </div>
        <div class="content">
            <p>Hola {{ $cita->cliente->name }},</p>
            <p>¡Excelente! Tu cita ha sido confirmada por nuestro equipo de recepción. Aquí están los detalles:</p>
            
            <div class="info-box">
                <p><strong>🐾 Mascota:</strong> {{ $cita->mascota->nombre }}</p>
                <p><strong>🛁 Servicio:</strong> {{ $cita->servicio->nombre }}</p>
                <p><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('l, d \\d\\e F \\d\\e Y', ['locale' => 'es']) }}</p>
                <p><strong>⏰ Hora:</strong> {{ $cita->hora_inicio }} - {{ $cita->hora_fin }}</p>
                <p><strong>💇 Groomer:</strong> {{ $cita->groomer->name }}</p>
                <p><strong>💰 Precio:</strong> Bs. {{ number_format($cita->servicio->precio, 2) }}</p>
            </div>

            <p><strong>Por favor, recuerda:</strong></p>
            <ul>
                <li>Llegue 10 minutos antes de la hora programada</li>
                <li>Asegúrate de que tu mascota esté en buenas condiciones sanitarias</li>
                <li>Recuerda traer la cartilla de vacunas si es la primera vez</li>
            </ul>

            <p><span class="badge">CONFIRMADA</span></p>
        </div>
        <div class="footer">
            <p>Spa de Mascotas - Sistema de Gestión</p>
            <p>© {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
