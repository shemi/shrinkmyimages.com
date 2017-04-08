<?php

namespace App\Shrink;

use App\BulkShrink;
use App\BulkShrinkImages;
use App\Events\BulkShrinkFail;
use App\File;
use App\Jobs\FinishBulkShrink;
use App\Jobs\ProcessNextBulkShrinkImage;
use App\Shrink;
use App\Shrink\Exceptions\BulkShrinkImageProcessFailException;
use App\Shrink\Exceptions\BulkShrinkRequestFailException;
use App\Shrink\Exceptions\OptimizerFailException;
use App\Shrink\Exceptions\RemoteFileHttpErrorException;
use App\Shrink\Exceptions\RemoteFileNotImageException;
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

    protected function callbackClient()
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

    protected function makeRequest($action, $json = [])
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

        dispatch(new ProcessNextBulkShrinkImage($this->bulkShrink, $this->shrink));
    }

    /**
     * @param BulkShrinkImages $image
     * @return bool
     */
    public function processImage(BulkShrinkImages $image)
    {
        $image->status = BulkShrinkImages::SUCCESS;
        $imageUrl = $image->url;

        //if the image url is not valid url...
        if(filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            //...if hte bulk object has base_url...
            $baseUrl = $this->bulkShrink->base_url;

            if(! empty($baseUrl)) {
                //...trim and build the url
                $baseUrl = trim(trim($baseUrl), '/');
                $imageUrl = trim(trim($imageUrl), '/');
                $imageUrl = "{$baseUrl}/$imageUrl";
            }

            //if its not valid url set invalid status and exit
            if(filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
                $image->status = BulkShrinkImages::INVALID_URL;
                $image->error_message = "The file URL is not valid";
                $image->save();

                return false;
            }
        }

        $remoteUploadFile = new GetRemoteImageRepository($imageUrl);
        $uploadRepository = new UploadRepository($remoteUploadFile);

        //try download the remote image
        try {
            $uploadFile = $remoteUploadFile->download();
        } catch (RemoteFileHttpErrorException $e) {
            $status = BulkShrinkImages::FAILED;

            switch ($e->getCode()) {
                case BulkShrinkImages::NOT_FOUND:
                    $status = BulkShrinkImages::NOT_FOUND;
                    break;
                case BulkShrinkImages::TIMEOUT:
                    $status = BulkShrinkImages::TIMEOUT;
                    break;
                case BulkShrinkImages::SERVER_ERROR:
                    $status = BulkShrinkImages::SERVER_ERROR;
                    break;
            }

            $image->status = $status;
            $image->error_message = "The remote server respond with the following status code: {$e->getCode()}";
            $image->save();

            return false;
        } catch (RemoteFileNotImageException $e) {
            $image->error_message = "The remote file is not a image.";
            $image->status = BulkShrinkImages::NOT_IMAGE;
            $image->save();

            return false;
        }

        $validator = Validator::make(
            ['isImage' => $uploadFile, 'sizeLimit' => $uploadFile],
            ['isImage' => 'image', 'sizeLimit' => 'max:20000']
        );

        //if the validation fails...
        if ($validator->fails()) {
            //...delete the temp file and set failed status...
            $remoteUploadFile->deleteTempFile();
            $image->status = BulkShrinkImages::FAILED;

            //...if its not image...
            if($validator->errors()->has('isImage')) {
                //...change status and error message
                $image->error_message = $validator->errors()->first('imageUrl');
                $image->status = BulkShrinkImages::NOT_IMAGE;

                return false;
            }
            //...if its pass the size limit...
            elseif ($validator->errors()->has('sizeLimit')) {
                //...change status and error message
                $image->error_message = $validator->errors()->first('sizeLimit');
                $image->status = BulkShrinkImages::TOO_BIG;

                return false;
            }

            //...save the image object and exit
            $image->save();

            return false;
        }

        try {
            $file = $uploadRepository->upload($uploadFile, $this->shrink);
        } catch (OptimizerFailException $e) {
            $image->status = BulkShrinkImages::OPTIMIZER_ERROR;
            $image->save();

            return false;
        }

        $image->file_id = $file->id;
        $image->status = BulkShrinkImages::SUCCESS_NOT_CHARGED;

        //if we reduced 5% or more...
        if($file->reduced_percentage > 5) {
            //...set status success
            $image->status = BulkShrinkImages::SUCCESS;
        }

        //update the shrink expiration date
        $this->shrink->expire_at = Carbon::now()->addDay();
        $this->shrink->save();
        $image->save();

        $this->sendDownloadUrl($file);
        $this->doNextAction();

        return true;
    }

    public function doNextAction()
    {
        //if we have more images to shrink...
        if($this->bulkShrink->hasNextImage()) {
            //...dispatch the next image job
            dispatch(new ProcessNextBulkShrinkImage(
                $this->bulkShrink,
                $this->shrink,
                $this->bulkShrink->nextImage()
            ));
        }
        //no more images...
        else {
            //...dispatch the finish bulk job
            dispatch(new FinishBulkShrink($this->bulkShrink, $this->shrink));
        }
    }

    public function finishBulkShrink()
    {
        $this->bulkShrink->status = BulkShrink::STATUS_FINISH;
        $this->bulkShrink->save();

        $user = $this->shrink->user;
        $userBalance = $user->balance;
        $images = $this->bulkShrink->images;
        $imagesToCharge = $images->where('status', BulkShrinkImages::SUCCESS);
        $imagesNotCharged = $images->where('status', BulkShrinkImages::SUCCESS_NOT_CHARGED);
        $failedImages = $images->where('status', '>', BulkShrinkImages::SUCCESS_NOT_CHARGED);

        $userBalance->subtractReserved($imagesNotCharged->count() + $failedImages->count(), false);
        $userBalance->chargeReserved($imagesToCharge->count());

        $this->makeRequest('finish', [
            'totalImages' => $images->count(),
            'totalChargedImages' => $imagesToCharge->count(),
            'totalSuccessButNoCharge' => $imagesNotCharged->count(),
            'totalFailedImages' => $failedImages->count(),
        ]);
    }

    protected function sendDownloadUrl(File $file)
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

    protected function failCallbackCacheKey()
    {
        return "bulk_shrink_{$this->bulkShrink->id}_callback_fails";
    }

    public function isFailedTooManyTimes()
    {
        return Cache::get($this->failCallbackCacheKey(), 0) >= static::MAX_ALLOWED_FAILS;
    }

    public function handelBulkCallbackFail($httpStatusCode, $statusMessage = "")
    {
        if($this->isFailedTooManyTimes()) {
            $this->cancelBulkShrink($httpStatusCode, $statusMessage);

            return;
        }

        Cache::increment($this->failCallbackCacheKey());
        $this->doNextAction();
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