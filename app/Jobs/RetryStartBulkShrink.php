<?php

namespace App\Jobs;

use App\BulkShrink;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RetryStartBulkShrink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var BulkShrink
     */
    private $bulkShrink;

    /**
     * Create a new job instance.
     *
     * @param BulkShrink $bulkShrink
     */
    public function __construct(BulkShrink $bulkShrink)
    {
        $this->bulkShrink = $bulkShrink;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

}
