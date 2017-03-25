<?php

namespace App\Http\Controllers\Api;

use App\Shrink\Repositories\GetRemoteImageRepository;
use App\Shrink\Repositories\ShrinkRepository;
use App\Shrink\Repositories\UploadRepository;
use App\User;
use Illuminate\Http\Request;
use Validator;

class ShrinkController extends ApiController
{

    public function shrink(Request $request, ShrinkRepository $shrinkRepository, UploadRepository $uploadRepository)
    {
        $this->validate($request, [
            'image' => 'required_without:imageUrl|image|max:20000',
            'imageUrl' => 'required_without:image|url'
        ]);

        /** @var User $user */
        $user = auth()->user();

        if(! $user->balance->haveFreeCredits()) {
            return $this->respondBadRequest('No enough credit');
        }

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

            if($validator->fails()) {
                $user->balance->subtractTotal();
                $remoteUploadFile->deleteTempFile();

                return $this->respondBadRequest($validator->errors());
            }

        }

        $file = $uploadRepository->upload($uploadFile, $shrink);

        if($shrink->percent < 5) {
            $user->balance->subtractTotal();
        }

        /*
         * if we download the remote file we need to delete him
         * since PHP won't delete him automatically
         */
        if (isset($remoteUploadFile)) {
            $remoteUploadFile->deleteTempFile();
        }



        $filePath = base_path("storage/app/{$file->path}");

        return response()
            ->download($filePath, $file->name, [
                'X-TotalShrinks' => $user->balance->total_used,
                'X-PrePaidShrinks-Remaining' => $user->balance->remainingFreeCredits(),
                'X-ShrinkPercent' => $shrink->percent
            ]);
    }

    public function bulk()
    {

    }

}