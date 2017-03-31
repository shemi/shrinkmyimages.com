<?php

namespace App\Http\Controllers\Api;

use App\Call;
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
        $amount = 0;

        $this->validate($request, [
            'image' => 'required_without:imageUrl|image|max:20000',
            'imageUrl' => 'required_without:image|url'
        ]);

        /** @var User $user */
        $user = auth()->user();

        if(! $user->balance->haveFreeCredits()) {
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

            if($validator->fails()) {
                $user->balance->subtractTotal();
                $remoteUploadFile->deleteTempFile();
                $this->createCallModel($request, $user, $shrink->id, 0);

                return $this->respondBadRequest($validator->errors());
            }

        }

        $file = $uploadRepository->upload($uploadFile, $shrink);

        if($shrink->percent < 5) {
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

    private function createCallModel(Request $request, User $user, $shrinkId, $credit, $action = 'shrink')
    {
        $call = new Call();
        $call->user_id = $user->id;
        $call->shrink_id = $shrinkId;
        $call->type = "ShrinkController@{$action}";
        $call->status = 2;
        $call->from_ip = $request->ip();
        $call->credit = $credit;
        $call->caller_identifier = $user->token()->id;
        $call->save();
    }

}