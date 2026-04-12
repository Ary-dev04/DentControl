<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1d4ed8, #14b8a6); padding: 40px 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 32px; }
        .header p { color: #CCffffff; margin: 8px 0 0; }
        .body { padding: 30px; text-align: center; }
        .emoji { font-size: 60px; margin: 20px 0; }
        .footer { background: #0f172a; color: #64748b; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🎂 ¡Feliz Cumpleaños!</h1>
        <p>De parte de todo el equipo de {{ $nombreClinica }}</p>
    </div>
    <div class="body">
        <div class="emoji">🎉</div>
        <h2>¡Hola, {{ $nombrePaciente }}!</h2>
        <p style="color:#64748b; line-height:1.6">
            En este día tan especial, todo el equipo de <strong>{{ $nombreClinica }}</strong>
            te desea un feliz cumpleaños. Esperamos que pases un día increíble rodeado
            de las personas que más quieres.
        </p>
        <p style="color:#14b8a6; font-weight:bold; margin-top:24px">
            ¡Recuerda que tu sonrisa es lo más importante! 🦷
        </p>
    </div>
    <div class="footer">DentControl — Tu salud dental, en tus manos</div>
</div>
</body>
</html>