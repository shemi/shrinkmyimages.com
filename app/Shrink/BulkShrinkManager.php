<?php

namespace App\Shrink;

use App\BulkShrink;
use App\Events\BulkShrinkFail;
use App\Shrink;
use Cache;

class BulkShrinkManager
{

    const MAX_ALLOWED_FAILS = 3;

    /**
     * @var BulkShrink
     */
    protected $bulkShrink;

    /**
     * @var Shrink
     */
    protected $shrink;

    public function __construct(BulkShrink $bulkShrink, Shrink $shrink = null)
    {

        $this->bulkShrink = $bulkShrink;

        if ($shrink) {
            $this->shrink = $shrink;
        } else {
            $this->shrink = $bulkShrink->shrink;
        }
    }

    public function testCallback()
    {

    }

    public function failCallbackCacheKey()
    {
        return "shrink_{$this->shrink->id}_callback_fails";
    }

    public function handelBulkCallbackFail($httpStatusCode, $statusMessage = "")
    {
        if(Cache::get($this->failCallbackCacheKey(), 0) >= static::MAX_ALLOWED_FAILS) {
            $this->cancelBulkShrink($httpStatusCode, $statusMessage);

            event(new BulkShrinkFail(
                $httpStatusCode,
                $this->bulkShrink,
                $this->shrink->user,
                $statusMessage
            ));
        }

        Cache::increment($this->failCallbackCacheKey());
    }

    public function cancelBulkShrink($httpStatusCode)
    {
        $statuses = BulkShrink::STATUS_CALLBACK_URL_ERRORS;
        $flippedStatuses = array_flip($statuses);
        $status = $statuses['GENERAL'];

        if(array_key_exists($httpStatusCode, $flippedStatuses)) {
            $status = $httpStatusCode;

        } elseif($httpStatusCode >= $statuses['REDIRECT'] && $httpStatusCode < $statuses['BAD_REQUEST']) {
            $status = $statuses['REDIRECT'];

        } elseif ($httpStatusCode >= $statuses['BAD_REQUEST'] && $httpStatusCode < $statuses['SERVER_ERROR']) {
            $status = $statuses['BAD_REQUEST'];

        } elseif ($httpStatusCode >= $statuses['SERVER_ERROR']) {
            $status = $statuses['SERVER_ERROR'];
        }

        $this->bulkShrink->status = $status;
        $this->bulkShrink->save();
    }

}