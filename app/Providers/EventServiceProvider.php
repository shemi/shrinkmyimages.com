<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\BulkShrinkCreated' => [
            'App\Listeners\ValidateCallbackUrl',
        ],
        'App\Events\BulkShrinkVerified' => [
            'App\Listeners\StartProcessingBulkShrink',
        ],
        'App\Events\BulkShrinkFail' => [
            'App\Listeners\CancelBulkShrink',
        ]

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
