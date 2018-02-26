<?php

namespace attendance\Listeners;

use attendance\Events\deleteMessage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class clients
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
     * @param  deleteMessage  $event
     * @return void
     */
    public function handle(deleteMessage $event)
    {
        //
    }
}
