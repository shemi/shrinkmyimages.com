<?php

namespace App\Listeners;

use App\Events\BulkShrinkVerificationFail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CancelBulkShrink
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
     * @param  BulkShrinkVerificationFail  $event
     * @return void
     */
    public function handle(BulkShrinkVerificationFail $event)
    {
        $bulkShrink = $event->bulkShrink;
        $shrink = $bulkShrink->shrink;



    }
}
