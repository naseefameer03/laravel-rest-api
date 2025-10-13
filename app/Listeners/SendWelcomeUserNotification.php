<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Mail\WelcomeUserMail;
use App\Notifications\WelcomeUserNotification;
use Illuminate\Support\Facades\Mail;

class SendWelcomeUserNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // Send welcome email
        Mail::to($event->user->email)->send(new WelcomeUserMail($event->user));

        // Send welcome notification
        $event->user->notify(new WelcomeUserNotification);
    }
}
