<?php

namespace App\Listeners;

use App\Events\UserStatusChanged;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountStatusChangeMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendUserStatusChangeEmail
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
    public function handle(UserStatusChanged $event): void
    {
        // Send email notification to the user
        Mail::to($event->user->email)
            ->send(new AccountStatusChangeMail($event->user, $event->statusMessage));
    }
}
