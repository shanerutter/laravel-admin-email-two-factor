<?php

namespace Shanerutter\LaravelAdminEmailTwoFactor\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class TwoFactorCode extends Mailable
{
    use Queueable, SerializesModels;

    private int $code;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(int $code)
    {
        $this->code = $code;

        $this->message = (new MailMessage())
        ->subject('Two Factor Code - ' . config('app.name'))
        ->line('Your two factor code is ' . $this->code . '.')
        ->action('Verify Here', route(admin_get_route('auth.2fa.email')))
        ->line('The code will expire in 10 minutes')
        ->line('If you have not tried to login, update your password immediately.');
    }

    public function build()
    {
        return $this->markdown('notifications::email', $this->message->data());
    }
}
