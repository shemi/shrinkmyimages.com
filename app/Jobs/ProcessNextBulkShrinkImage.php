<?php

namespace App\Jobs;

use App\BulkShrink;
use App\Shrink\BulkShrinkManager;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessNextBulkShrinkImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bulkShrinkManager;

    /**
     * @var BulkShrink
     */
    public $bulkShrink;

    /**
     * Create a new job instance.
     *
     * @param BulkShrink $bulkShrink
     */
    public function __construct(BulkShrink $bulkShrink)
    {
        $this->bulkShrink = $bulkShrink;
        $this->bulkShrinkManager = new BulkShrinkManager($bulkShrink, $bulkShrink->shrink);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->bulkShrinkManager->processNextImage();
    }



}
