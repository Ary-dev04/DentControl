<?php

namespace App\Mail;

use App\Models\Cita;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecordatorioCitaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Cita $cita,
        public string $nombrePaciente,
        public string $horasAntes
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '⏰ Recordatorio de tu cita - DentControl');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.recordatorio_cita');
    }
}