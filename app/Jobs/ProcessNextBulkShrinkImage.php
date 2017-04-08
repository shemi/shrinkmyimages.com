<?php

namespace App\Jobs;

use App\BulkShrink;
use App\BulkShrinkImages;
use App\Shrink;
use App\Shrink\BulkShrinkManager;
use App\Shrink\Exceptions\BulkShrinkRequestFailException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessNextBulkShrinkImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $bulkShrinkManager;

    /**
     * @var BulkShrink
     */
    public $bulkShrink;

    public $nextBulkImage;

    /**
     * Create a new job instance.
     *
     * @param BulkShrink $bulkShrink
     * @param Shrink $shrink
     * @param BulkShrinkImages $bulkShrinkImages
     */
    public function __construct(BulkShrink $bulkShrink, Shrink $shrink, BulkShrinkImages $bulkShrinkImages = null)
    {
        $this->bulkShrink = $bulkShrink;
        $this->bulkShrinkManager = new BulkShrinkManager($bulkShrink, $shrink);

        if(! $bulkShrinkImages) {
            $bulkShrinkImages = $bulkShrink->nextImage();
        }

        $this->nextBulkImage = $bulkShrinkImages;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(! $this->bulkShrinkManager->processImage($this->nextBulkImage)) {
            $this->bulkShrinkManager->doNextAction();
        }
    }


    public function failed(\Exception $e)
    {
        if($e instanceof BulkShrinkRequestFailException) {
            $this->bulkShrinkManager->handelBulkCallbackFail($e->getCode());

            return;
        }

        $this->bulkShrinkManager->doNextAction();
    }

}
