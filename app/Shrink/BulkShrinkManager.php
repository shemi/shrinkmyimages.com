<?php

namespace App\Shrink;

use App\BulkShrink;
use App\BulkShrinkImages;
use App\Events\BulkShrinkFail;
use App\File;
use App\Jobs\ProcessNextBulkShrinkImage;
use App\Shrink;
use App\Shrink\Exceptions\BulkShrinkRequestFailException;
use App\Shrink\Repositories\GetRemoteImageRepository;
use App\Shrink\Repositories\UploadRepository;
use Cache;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Validator;

class BulkShrinkManager
{

    const MAX_ALLOWED_FAILS = 3;

    /**
     * @var Client
     */
    protected $client;

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

    public function callbackClient()
    {
        if($this->client) {
            return $this->client;
        }

        $clientOptions = [
            'base_uri' => $this->bulkShrink->callback_url,
            'allow_redirects' => false,
            'connect_timeout' => 5.0,
            'headers' => [
                'X-App-ID' => ''
            ]
        ];

        if($this->bulkShrink->security_type === 'basic') {
            $clientOptions['auth'] = [
                $this->bulkShrink->security_fields['username'],
                $this->bulkShrink->security_fields['password']
            ];
        }

        if($this->bulkShrink->security_type === 'token') {
            $clientOptions['headers']['Authorization'] = "Bearer {$this->bulkShrink->security_fields}";
        }

        $this->client = new Client($clientOptions);

        return $this->client;
    }

    public function makeRequest($action, $json = [])
    {
        try {
            $response = $this->callbackClient()->request('POST', '', [
                'form_params' => array_merge(
                    ['action' => $action],
                    $json
                )
            ]);
        } catch (RequestException $e) {
            throw new BulkShrinkRequestFailException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        return $response;
    }

    public function startProcessing()
    {
        $this->bulkShrink->status = BulkShrink::STATUS_PROCESSING;
        $this->bulkShrink->save();

        dispatch(new ProcessNextBulkShrinkImage($this->bulkShrink));
    }

    public function processNextImage()
    {
        /** @var BulkShrinkImages $image */
        $image = $this->bulkShrink->nextImage();

        if(! $image) {
            $this->finishBulkShrink();

            return;
        }

        $image->status = BulkShrinkImages::SUCCESS;
        $imageUrl = $image->url;

        if(! empty($this->bulkShrink->base_url)) {
            $imageUrl = trim(trim($this->bulkShrink->base_url), '/') .
                        '/' .
                        trim(trim($image->url), '/');
        }

        $remoteUploadFile = new GetRemoteImageRepository($imageUrl);
        $uploadRepository = new UploadRepository($remoteUploadFile);
        $uploadFile = $remoteUploadFile->download();

        $validator = Validator::make(
            ['imageUrl' => $uploadFile],
            ['imageUrl' => 'image|max:20000']
        );

        if ($validator->fails()) {
            $remoteUploadFile->deleteTempFile();
            $image->error_message = $validator->errors()->first('imageUrl');
            $image->status = BulkShrinkImages::NOT_IMAGE;
            $image->save();

            return;
        }

        $file = $uploadRepository->upload($uploadFile, $this->shrink);
        $image->file_id = $file->id;
        $image->status = BulkShrinkImages::SUCCESS_NOT_CHARGED;

        if($file->reduced_percentage > 5) {
            $image->status = BulkShrinkImages::SUCCESS;
            $this->shrink->user->balance->chargeReserved();
        } else {
            $this->shrink->user->balance->subtractReserved();
        }

        $this->shrink->expire_at = Carbon::now()->addDay();
        $this->shrink->save();

        $image->save();
        $this->sendDownloadUrl($file);

        dispatch(new ProcessNextBulkShrinkImage($this->bulkShrink));
    }

    public function finishBulkShrink()
    {
        $this->bulkShrink->status = BulkShrink::STATUS_FINISH;
        $this->bulkShrink->save();
    }

    public function sendDownloadUrl(File $file)
    {
        $this->makeRequest('download', [
            'beforeSize' => $file->size_before,
            'afterSize' => $file->size_after,
            'fileType' => $file->ext,
            'fileName' => $file->name,
            'downloadUrlExpiredAt' => $this->shrink->expire_at->toDateTimeString(),
            'downloadUrl' => $file->downloadUrl($this->shrink)
        ]);
    }

    public function testCallback()
    {
        $this->makeRequest('test');

        return true;
    }

    public function failCallbackCacheKey()
    {
        return "shrink_{$this->shrink->id}_callback_fails";
    }

    public function handelBulkCallbackFail($httpStatusCode, $statusMessage = "")
    {
        if(Cache::get($this->failCallbackCacheKey(), 0) >= static::MAX_ALLOWED_FAILS) {
            $this->cancelBulkShrink($httpStatusCode, $statusMessage);
        }

        Cache::increment($this->failCallbackCacheKey());
    }

    public function cancelBulkShrink($httpStatusCode, $statusMessage = "")
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

        event(new BulkShrinkFail(
            $httpStatusCode,
            $this->bulkShrink,
            $this->shrink->user,
            $statusMessage
        ));
    }

}