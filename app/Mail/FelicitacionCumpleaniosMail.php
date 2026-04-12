<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FelicitacionCumpleaniosMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $nombrePaciente,
        public string $nombreClinica
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '🎂 ¡Feliz cumpleaños! - DentControl');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.cumpleanos');
    }
}