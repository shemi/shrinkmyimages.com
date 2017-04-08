<?php

namespace App\Jobs;

use App\BulkShrink;
use App\Shrink;
use App\Shrink\BulkShrinkManager;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FinishBulkShrink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $bulkShrinkManager;
    /**
     * @var BulkShrink
     */
    private $bulkShrink;

    /**
     * Create a new job instance.
     *
     * @param BulkShrink $bulkShrink
     * @param Shrink $shrink
     */
    public function __construct(BulkShrink $bulkShrink, Shrink $shrink)
    {
        $this->bulkShrink = $bulkShrink;
        $this->bulkShrinkManager = new BulkShrinkManager($bulkShrink, $shrink);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->bulkShrinkManager->finishBulkShrink();
    }
}
