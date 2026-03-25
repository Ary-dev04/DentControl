<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #334155; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        .header { background: #1e293b; color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: white; }
        .credentials-box { background: #f1f5f9; border-radius: 8px; padding: 20px; margin: 20px 0; border: 1px dashed #cbd5e1; }
        .user-row { margin-bottom: 10px; font-size: 16px; }
        .label { font-weight: bold; color: #64748b; text-transform: uppercase; font-size: 12px; display: block; }
        .value { font-family: monospace; font-size: 18px; color: #0f172a; font-weight: bold; }
        .footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; }
        .button { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">DentControl Móvil</h1>
        </div>
        
        <div class="content">
            <h2 style="color: #1e293b;">¡Hola, {{ $nombre }}!</h2>
            <p>Tu especialista ha habilitado tu acceso a nuestra <strong>aplicación móvil oficial</strong>. Desde ella podrás consultar tus citas, historial y planes de tratamiento.</p>
            
            <p>Aquí tienes tus credenciales de acceso:</p>
            
            <div class="credentials-box">
                <div class="user-row">
                    <span class="label">Usuario:</span>
                    <span class="value">{{ $usuario }}</span>
                </div>
                <div class="user-row">
                    <span class="label">Contraseña Temporal:</span>
                    <span class="value">{{ $password }}</span>
                </div>
            </div>

            <p style="font-size: 14px; color: #ef4444;">* Por seguridad, se te pedirá cambiar esta contraseña la primera vez que ingreses.</p>
            
            <center>
                <a href="#" class="button">Descargar Aplicación</a>
            </center>
        </div>

        <div class="footer">
            Este es un correo automático enviado por el sistema DentControl.<br>
            Si no reconoces esta actividad, por favor contacta a tu clínica dental.
        </div>
    </div>
</body>
</html>