<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;


class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $passwordTemporal;
    public $emailNuevo;

    public function __construct($passwordTemporal = null, $emailNuevo = null)
    {
        $this->passwordTemporal = $passwordTemporal;
        $this->emailNuevo = $emailNuevo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    protected function verificationUrl($notifiable)
    {
        $expiration = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));


        $email = $this->emailNuevo?? $notifiable->getEmailForVerification();

        return URL::temporarySignedRoute(
            'verification.verify',
            $expiration,
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($email),
                'email' => $this->emailNuevo,
            ]
        );
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->verificationUrl($notifiable);

        // Usamos view() para renderizar un Blade HTML personalizado.
        return (new MailMessage)
            ->subject('Verificación de correo')
            ->view('Email.verifyEmail', [
                'url'  => $url,
                'user' => $notifiable,
                'passwordTemporal' => $this->passwordTemporal,
                'emailNuevo' => $this->emailNuevo,

            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
