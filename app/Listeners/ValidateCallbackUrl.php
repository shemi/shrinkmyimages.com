<?php

namespace App\Listeners;

use App\Events\BulkShrinkCreated;
use App\Shrink\BulkShrinkManager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ValidateCallbackUrl
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
     * @param  BulkShrinkCreated  $event
     * @return void
     */
    public function handle(BulkShrinkCreated $event)
    {
        $bulkShrinkManager = new BulkShrinkManager($event->bulkShrink, $event->shrink);



    }
}
