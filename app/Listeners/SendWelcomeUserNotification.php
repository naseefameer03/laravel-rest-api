<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Notifications\WelcomeUserNotification;

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
        $event->user->notify(new WelcomeUserNotification);
    }
}
