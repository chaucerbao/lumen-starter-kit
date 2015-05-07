<?php

namespace App\Jobs;

use App\User;
use Illuminate\Support\Facades\Mail;

class RegistrationConfirmationEmail extends Job
{
    /**
     * The recipient of the email.
     *
     * @var User
     */
    protected $user;

    /**
     * The user's confirmation token.
     *
     * @var string
     */
    protected $token;

    /**
     * Create a new command instance.
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Send a registration confirmation e-mail.
     *
     * @param User   $user
     * @param string $token
     */
    public function handle()
    {
        $user = $this->user;
        $token = $this->token;

        Mail::send('auth.email.registration_confirmation', compact('user', 'token'), function ($message) {
            $message
                ->from(env('MAIL_FROM_ADDRESS', 'support@site.com'), env('MAIL_FROM_NAME', 'Customer Support'))
                ->to($this->user->email, $this->user->fullName)
                ->subject('Registration Confirmation');
        });
    }
}
