<?php

namespace App\Jobs;

use App\BulkShrink;
use App\Shrink;
use App\Shrink\BulkShrinkManager;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StartBulkShrink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = BulkShrinkManager::MAX_ALLOWED_FAILS;

    /**
     * @var BulkShrink
     */
    private $bulkShrink;

    private $bulkShrinkManager;
    /**
     * @var Shrink
     */
    private $shrink;

    /**
     * Create a new job instance.
     *
     * @param BulkShrink $bulkShrink
     * @param Shrink $shrink
     */
    public function __construct(BulkShrink $bulkShrink, Shrink $shrink)
    {
        $this->bulkShrink = $bulkShrink;
        $this->shrink = $shrink;
        $this->bulkShrinkManager = new BulkShrinkManager($bulkShrink, $shrink);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->bulkShrinkManager->testCallback()) {
            $this->bulkShrinkManager->startProcessing();
        }
    }

    /**
     * @param Exception|null $exception
     */
    public function failed(Exception $exception = null)
    {
        $this
            ->bulkShrinkManager
            ->cancelBulkShrink($exception->getCode(), $exception->getMessage());
    }
}
