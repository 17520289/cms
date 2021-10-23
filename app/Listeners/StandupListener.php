<?php

namespace App\Listeners;

use App\Events\NewStandup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\User;
use App\Notifications\SendNewStandup;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
class StandupListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewStandup  $event
     * @return void
     */
    public function handle(NewStandup $event)
    {
        $user = Auth::user();
        Notification::send($user, new SendNewStandup($event->standup, $event->yesterday));
    }
}
