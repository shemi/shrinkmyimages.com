<?php

namespace App\Listeners;

use App\Events\BulkShrinkVerified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StartProcessingBulkShrink
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
     * @param  BulkShrinkVerified  $event
     * @return void
     */
    public function handle(BulkShrinkVerified $event)
    {
        //
    }
}
