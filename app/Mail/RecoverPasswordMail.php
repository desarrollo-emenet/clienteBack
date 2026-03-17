<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
//use Illuminate\Support\Facades\Log;

class RecoverPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $URLAPI;
    public $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        //Transformar el token a string en caso de que venga como objeto
        $this->token = is_object($token) ? (string) ($token->token ?? json_encode($token)) : (string) $token;
        //obtener la URL del frontend desde el .env
        $this->URLAPI = env('FRONTEND_URL_LOCAL');
        //Generar la URL de recuperacion de contraseña
        $this->url = $this->URLAPI . '/response-password?token=' . urlencode($this->token);
        //Log::info('Recover URL: ' . $this->url);
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Recuperacion de contraseña',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
     return new Content(
            view: 'email.passwordRecover', // vista blade
            with: [
                'token' => $this->token,
                'url'   => $this->url,
            ]
            
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
