<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; }
        .header { color: #2c3e50; border-bottom: 3px solid #9b59b6; padding-bottom: 15px; margin-bottom: 20px; }
        .content { color: #34495e; line-height: 1.6; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ecf0f1; font-size: 12px; color: #7f8c8d; }
        .success-box { background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 15px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🐾 ¡{{ $cita->mascota->nombre }} está lista para recoger!</h2>
        </div>
        <div class="content">
            <p>Hola {{ $cita->cliente->name }},</p>
            <p>¡Excelente noticia! El servicio de grooming para {{ $cita->mascota->nombre }} ha sido completado exitosamente.</p>
            
            <div class="success-box">
                <p><strong>✨ Servicio realizado:</strong> {{ $cita->servicio->nombre }}</p>
                <p><strong>💇 Atendida por:</strong> {{ $cita->groomer->name }}</p>
                <p><strong>📅 Fecha:</strong> {{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
                <p><strong>⏰ Hora de finalización:</strong> {{ now()->format('H:i') }}</p>
            </div>

            <p><strong>Por favor, recuerda:</strong></p>
            <ul>
                <li>Ven a recoger a tu mascota lo antes posible</li>
                <li>Verifica que tu mascota se vea bien y esté feliz</li>
                <li>Consulta con el groomer sobre recomendaciones de cuidado</li>
            </ul>

            <p>¡Gracias por confiar en nuestro Spa de Mascotas! 🎉</p>
        </div>
        <div class="footer">
            <p>Spa de Mascotas - Sistema de Gestión</p>
            <p>© {{ date('Y') }} Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
