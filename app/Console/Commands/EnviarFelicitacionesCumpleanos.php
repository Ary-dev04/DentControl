<?php

namespace App\Console\Commands;

use App\Mail\FelicitacionCumpleaniosMail;
use App\Models\Paciente;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarFelicitacionesCumpleanos extends Command
{
    protected $signature   = 'pacientes:cumpleanos';
    protected $description = 'Envía felicitaciones de cumpleaños a pacientes';

    public function handle(FcmService $fcm): void
    {
        $hoy = Carbon::now();

        $pacientes = Paciente::with(['clinica', 'accesoMovil'])
            ->whereNotNull('fecha_nacimiento')
            ->whereMonth('fecha_nacimiento', $hoy->month)
            ->whereDay('fecha_nacimiento',   $hoy->day)
            ->where('estatus', 'activo')
            ->get();

        foreach ($pacientes as $paciente) {
            $nombre  = trim("{$paciente->nombre} {$paciente->apellido_paterno}");
            $correo  = $paciente->email;
            $clinica = $paciente->clinica?->nombre ?? 'DentControl';
            $acceso  = $paciente->accesoMovil;

            // Correo al paciente
            if ($correo) {
                try {
                    Mail::to($correo)->send(
                        new FelicitacionCumpleaniosMail($nombre, $clinica)
                    );
                    $this->info("Felicitación enviada a {$correo}");
                } catch (\Throwable $e) {
                    Log::error("Error correo cumpleaños: " . $e->getMessage());
                }
            }

            // Push notification
            if ($acceso?->fcm_token) {
                $fcm->enviar(
                    $acceso->fcm_token,
                    '🎂 ¡Feliz cumpleaños!',
                    "Todo el equipo de {$clinica} te desea un feliz cumpleaños 🎉"
                );
                $this->info("Push cumpleaños enviado a {$nombre}");
            }
        }

        $this->info('Felicitaciones procesadas.');
    }
}