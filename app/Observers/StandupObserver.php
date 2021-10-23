<?php

namespace App\Observers;

use App\Standup;
use App\Events\NewStandup;

class StandupObserver
{
    /**
     * Handle the standup "created" event.
     *
     * @param  \App\Standup  $standup
     * @return void
     */
    public function created(Standup $standup)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new NewStandup($standup, request()->yesterday));
        }
    }

    /**
     * Handle the standup "updated" event.
     *
     * @param  \App\Standup  $standup
     * @return void
     */
    public function updated(Standup $standup)
    {
        //
    }

    /**
     * Handle the standup "deleted" event.
     *
     * @param  \App\Standup  $standup
     * @return void
     */
    public function deleted(Standup $standup)
    {
        //
    }

    /**
     * Handle the standup "restored" event.
     *
     * @param  \App\Standup  $standup
     * @return void
     */
    public function restored(Standup $standup)
    {
        //
    }

    /**
     * Handle the standup "force deleted" event.
     *
     * @param  \App\Standup  $standup
     * @return void
     */
    public function forceDeleted(Standup $standup)
    {
        //
    }
}
