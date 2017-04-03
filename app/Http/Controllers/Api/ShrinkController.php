<?php

namespace App\Http\Controllers\Api;

use App\BulkShrink;
use App\Call;
use App\Shrink;
use App\Shrink\Repositories\GetRemoteImageRepository;
use App\Shrink\Repositories\ShrinkRepository;
use App\Shrink\Repositories\UploadRepository;
use App\User;
use Hashids;
use Illuminate\Http\Request;
use Validator;

class ShrinkController extends ApiController
{

    public function shrink(Request $request, ShrinkRepository $shrinkRepository, UploadRepository $uploadRepository)
    {
        $amount = 0;

        $this->validate($request, [
            'image' => 'required_without:imageUrl|image|max:20000',
            'imageUrl' => 'required_without:image|url'
        ]);

        /** @var User $user */
        $user = auth()->user();

        if (!$user->balance->haveFreeCredits()) {
            return $this->respondBadRequest('No enough credit');
        }

        $amount = 1;
        $user->balance->addTotal();
        $shrink = $shrinkRepository->create($request, $user, 'api');

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

        $file = $uploadRepository->upload($uploadFile, $shrink);

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

    public function bulk(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $validationRules = [
            'callbackUrl' => 'required|url',
            'securityType' => 'in:basic,token',
            'images' => 'required|array|min:1',
            'images.*' => 'string',
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

        $images = $images
            ->map(function ($image) {
                return trim(trim($image, '/'));
            })
            ->reject(function ($image) {
                return empty($image);
            });

        if ($images->isEmpty()) {
            return $this->respondUnprocessableEntity([
                'images' => 'The images field is empty'
            ]);
        }

        $shrink = new Shrink();
        $bulkShrink = new BulkShrink();

        if($user->balance->remainingFreeCredits() < $images->count()) {

        } else {
            $user->balance->addReserved($images->count());
        }

    }

}