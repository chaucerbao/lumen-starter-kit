<?php

namespace App\Jobs;

use App\User;
use Illuminate\Support\Facades\Mail;

class SendPendingUpdateConfirmationRequestEmail extends Job
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
     * The e-mail's subject line.
     *
     * @var string
     */
    protected $subject;

    /**
     * The e-mail view to use.
     *
     * @var string
     */
    protected $view;

    /**
     * Create a new command instance.
     *
     * @param User   $user
     * @param string $token
     * @param string $subject
     * @param string $view
     */
    public function __construct(User $user, $token, $subject, $view)
    {
        $this->user = $user;
        $this->token = $token;
        $this->subject = $subject;
        $this->view = $view;
    }

    /**
     * Send a pending update confirmation request e-mail.
     */
    public function handle()
    {
        $user = $this->user;
        $token = $this->token;
        $subject = $this->subject;

        Mail::send($this->view, compact('user', 'token'), function ($message) use ($user, $subject) {
            $message
                ->from(env('MAIL_FROM_ADDRESS', 'support@site.com'), env('MAIL_FROM_NAME', 'Support'))
                ->to($user->email, $user->fullName)
                ->subject($subject);
        });
    }
}
