<?php

namespace App\Http\Controllers\Api;

use App\BulkShrink;
use App\BulkShrinkImages;
use App\Call;
use App\Shrink;
use App\Shrink\Repositories\GetRemoteImageRepository;
use App\Shrink\Repositories\ShrinkRepository;
use App\Shrink\Repositories\UploadRepository;
use App\User;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;

class ShrinkController extends ApiController
{

    /**
     * @var ShrinkRepository
     */
    private $shrinkRepository;
    /**
     * @var UploadRepository
     */
    private $uploadRepository;

    public function __construct(ShrinkRepository $shrinkRepository, UploadRepository $uploadRepository)
    {

        $this->shrinkRepository = $shrinkRepository;
        $this->uploadRepository = $uploadRepository;
    }

    public function shrink(Request $request)
    {
        $amount = 0;

        $this->validate($request, [
            'image' => 'required_without:imageUrl|image|max:20000',
            'imageUrl' => 'required_without:image|url'
        ]);

        /** @var User $user */
        $user = auth()->user();

        if (!$user->balance->haveFreeCredits()) {
            return $this->respondNoEnoughCredit();
        }

        $amount = 1;
        $user->balance->addTotal();
        $shrink = $this->shrinkRepository->create($request, $user, 'api');

        if (!empty($request->file('image'))) {
            $uploadFile = $request->file('image');
        } else {
            $remoteUploadFile = new GetRemoteImageRepository($request->input('imageUrl'));
            $uploadFile = $remoteUploadFile->download();

            $validator = Validator::make(
                [
                    'imageUrl' => $uploadFile,
                ],
                [
                    'imageUrl' => 'image|max:20000',
                ]
            );

            if ($validator->fails()) {
                $user->balance->subtractTotal();
                $remoteUploadFile->deleteTempFile();
                $this->createCallModel($request, $user, $shrink->id, 0);

                return $this->respondBadRequest($validator->errors());
            }

        }

        $file = $this->uploadRepository->upload($uploadFile, $shrink);

        if ($shrink->percent < 5) {
            $user->balance->subtractTotal();
            $amount = 0;
        }

        /*
         * if we download the remote file we need to delete him
         * since PHP won't delete him automatically
         */
        if (isset($remoteUploadFile)) {
            $remoteUploadFile->deleteTempFile();
        }


        $this->createCallModel($request, $user, $shrink->id, $amount);

        $downloadId = Hashids::connection('shrinkFile')->encode([$shrink->id, $file->id]);

        return $this->respond(
            [
                'shrinkPercent' => $shrink->percent,
                'beforeSize' => $file->size_before,
                'afterSize' => $file->size_after,
                'fileType' => $file->ext,
                'fileName' => $file->name,
                'downloadUrlExpiredAt' => $shrink->expire_at->toDateTimeString(),
                'downloadUrl' => url("api/v1/download/{$downloadId}/{$file->name}")
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulk(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $validationRules = [
            'callbackUrl' => 'required|url',
            'securityType' => 'in:basic,token',
            'images' => 'required|array|min:1',
            'images.*.url' => 'required|string',
            'images.*.data' => 'json',
            'baseUrl' => 'url',
            'data' => 'json'
        ];

        $v = Validator::make($request->all(), $validationRules);

        $v->sometimes('securityToken', 'required|string', function ($input) {
            return $input->securityType == 'token';
        });

        $v->sometimes('securityToken.username', 'required|string', function ($input) {
            return $input->securityType == 'basic';
        });

        $v->sometimes('securityToken.password', 'required|string', function ($input) {
            return $input->securityType == 'basic';
        });

        if ($v->fails()) {
            return $this->respondBadRequest($v->errors());
        }

        $images = collect($request->input('images'));
        $baseUrl = $request->input('baseUrl', '');
        $baseUrl = trim($baseUrl, '/');
        $data = $request->input('data', []);
        $securityType = $request->input('securityType', 'none');
        $securityToken = $request->input('securityToken', []);
        $callbackUrl = $request->input('callbackUrl');

        if($securityType == 'token' && is_string($securityToken)) {
            $securityToken = ['token' => $securityToken];
        }

        $images = $images
            ->map(function ($image) {
                $image['url'] = trim(trim($image['url'], '/'));
                $image['data'] = isset($image['data']);
                $image['status'] = BulkShrinkImages::CREATED;

                return $image;
            })
            ->unique('url')
            ->reject(function (Collection $image) {
                return empty($image->get('url'));
            });

        if ($images->isEmpty()) {
            return $this->respondUnprocessableEntity([
                'images' => 'The images field is empty'
            ]);
        }

        if($user->balance->remainingFreeCredits() < $images->count()) {
            $this->createCallModel($request, $user, null, 0, 'bulk');

            return $this->respondNoEnoughCredit();
        }

        $user->balance->addReserved($images->count());

        $shrink = $this->shrinkRepository->create($request, $user, 'api');
        $call = $this->createCallModel($request, $user, $shrink->id, 0, 'bulk');

        /** @var BulkShrink $bulkShrink */
        $bulkShrink = BulkShrink::create([
            'shrink_id' => $shrink->id,
            'call_id' => $call->id,
            'status' => BulkShrink::STATUS_CREATED,
            'callback_url' => $callbackUrl,
            'base_url' => $baseUrl,
            'extra_fields' => $data,
            'images' => $images,
            'security_type' => $securityType,
            'security_fields' => $securityToken,
        ]);

        $bulkShrink->images()->createMany($images->toArray());

        $bulkShrinkPublicId = Hashids::connection('bulkShrink')
            ->encode([$shrink->id, $bulkShrink->id]);



        return $this->respond([
            'totalImages' => $images->count(),
            'bulkId' => $bulkShrinkPublicId,
            'statusUrl' => url("api/v1/shrink/{$bulkShrinkPublicId}/status")
        ]);
    }

}