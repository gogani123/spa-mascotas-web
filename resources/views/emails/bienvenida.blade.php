<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; }
        .container { max-w-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 20px; }
        h1 { color: #4f46e5; font-size: 24px; }
        .credentials { background-color: #f8fafc; padding: 15px; border-left: 4px solid #4f46e5; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenido a Spa Mascotas</h1>
        </div>
        
        <p>Hola <strong>{{ $user->name }}</strong>,</p>
        <p>El administrador del sistema te ha creado una cuenta oficial para que puedas acceder a tu panel de trabajo.</p>
        
        <div class="credentials">
            <p><strong>Tu Correo (Usuario):</strong> {{ $user->email }}</p>
            <p><strong>Tu Contraseña Temporal:</strong> {{ $password }}</p>
        </div>

        <p><em>Por favor, inicia sesión en el sistema y cambia tu contraseña lo antes posible por seguridad.</em></p>
        
        <div class="footer">
            <p>Este es un mensaje automático del Sistema de Spa Mascotas. Por favor, no respondas a este correo.</p>
        </div>
    </div>
</body>
</html>