<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioCitaMail;
use App\Models\Cita;
use App\Models\AccesoMovil;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EnviarRecordatoriosCitas extends Command
{
    protected $signature   = 'citas:recordatorios';
    protected $description = 'Envía recordatorios de citas 24h y 1h antes';

    public function handle(FcmService $fcm): void
    {
        $ahora    = Carbon::now();
        $en24h    = $ahora->copy()->addHours(24);
        $en1h     = $ahora->copy()->addHour();

        // Ventana de ±15 minutos para no enviar duplicados
        $ventana = 15;

        $ventanas = [
            ['tiempo' => $en24h, 'label' => 'en 24 horas'],
            ['tiempo' => $en1h,  'label' => 'en 1 hora'],
        ];

        foreach ($ventanas as $v) {
            $desde = $v['tiempo']->copy()->subMinutes($ventana);
            $hasta = $v['tiempo']->copy()->addMinutes($ventana);

            $citas = Cita::with(['paciente.accesoMovil'])
                ->where('estatus_cita', 'programada')
                ->whereDate('fecha', $v['tiempo']->toDateString())
                ->whereTime('hora', '>=', $desde->toTimeString())
                ->whereTime('hora', '<=', $hasta->toTimeString())
                ->get();

            foreach ($citas as $cita) {
                $paciente = $cita->paciente;
                if (!$paciente) continue;

                $nombre = trim("{$paciente->nombre} {$paciente->apellido_paterno}");
                $correo = $paciente->email;
                $acceso = $paciente->accesoMovil;

                // Correo
                if ($correo) {
                    try {
                        Mail::to($correo)->send(
                            new RecordatorioCitaMail($cita, $nombre, $v['label'])
                        );
                        $this->info("Correo enviado a {$correo}");
                    } catch (\Throwable $e) {
                        Log::error("Error correo recordatorio: " . $e->getMessage());
                    }
                }

                // Push notification
                if ($acceso?->fcm_token) {
                    $fcm->enviar(
                        $acceso->fcm_token,
                        '⏰ Recordatorio de cita',
                        "Tienes una cita {$v['label']} a las {$cita->hora}"
                    );
                    $this->info("Push enviado a {$nombre}");
                }
            }
        }

        $this->info('Recordatorios procesados.');
    }
}