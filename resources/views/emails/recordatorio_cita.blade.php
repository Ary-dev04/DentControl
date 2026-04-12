<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f8; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1d4ed8, #14b8a6); padding: 30px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .body { padding: 30px; }
        .card { background: #f8fafc; border-left: 4px solid #14b8a6; padding: 16px; border-radius: 8px; margin: 20px 0; }
        .label { font-size: 12px; color: #64748b; text-transform: uppercase; }
        .value { font-size: 18px; font-weight: bold; color: #1e293b; margin-top: 4px; }
        .footer { background: #0f172a; color: #64748b; padding: 20px; text-align: center; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⏰ Recordatorio de Cita</h1>
    </div>
    <div class="body">
        <p>Hola, <strong>{{ $nombrePaciente }}</strong></p>
        <p>Te recordamos que tienes una cita <strong>{{ $horasAntes }}</strong>:</p>
        <div class="card">
            <div class="label">Fecha</div>
            <div class="value">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</div>
            <div class="label" style="margin-top:12px">Hora</div>
            <div class="value">{{ $cita->hora }}</div>
            @if($cita->motivo_consulta)
            <div class="label" style="margin-top:12px">Motivo</div>
            <div class="value" style="font-size:14px">{{ $cita->motivo_consulta }}</div>
            @endif
        </div>
        <p style="color:#64748b; font-size:13px">Por favor llega 10 minutos antes de tu cita.</p>
    </div>
    <div class="footer">DentControl — Tu salud dental, en tus manos</div>
</div>
</body>
</html>