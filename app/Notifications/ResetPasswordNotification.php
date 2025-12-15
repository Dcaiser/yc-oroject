<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = config('app.url') . route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false);

        return (new MailMessage)
            ->subject('Reset Password - Yatim Center Al-Ruhamaa')
            ->view('emails.auth.reset-password', [
                'appName' => config('app.name', 'Yatim Center Al-Ruhamaa'),
                'resetUrl' => $url,
                'userName' => $notifiable->name ?? 'Pengguna',
            ]);
    }
}
