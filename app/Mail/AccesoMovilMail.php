<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccesoMovilMail extends Mailable
{
    use Queueable, SerializesModels;

    // Definimos las variables públicas para que estén disponibles en la vista automáticamente
    public $usuario;
    public $password;
    public $nombre;

    /**
     * El constructor recibe los datos desde el Controlador
     */
    public function __construct($usuario, $password, $nombre)
    {
        $this->usuario = $usuario;
        $this->password = $password;
        $this->nombre = $nombre;
    }

    /**
     * Configuramos el sobre del correo (Asunto)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tus credenciales de acceso - App Móvil DentControl',
        );
    }

    /**
     * Definimos qué vista se va a usar para el diseño del correo
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.acceso_movil', // Asegúrate de que el archivo esté en resources/views/emails/acceso_movil.blade.php
        );
    }

    /**
     * Adjuntos (en este caso vacío)
     */
    public function attachments(): array
    {
        return [];
    }
}