<?php

namespace App\Http\Controllers\Api;

use App\Shrink\Repositories\ShrinkRepository;
use App\Shrink\Repositories\UploadRepository;
use App\User;
use Illuminate\Http\Request;

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

        $shrink = $shrinkRepository->create($request, $user);
        $file = $uploadRepository->upload($request, $shrink);

        //make shure the user have credit
        //create shrink
        //save file
        //shrink
        //if success shrink eg > 2% less file size
        //charge the user

        $filePath = base_path("storage/app/{$file->path}");

        return response()
            ->download($filePath, $file->name, [
                'X-TotalShrinks' => $user->balance->total_used,
                'X-PrePaidShrinks-Remaining' => $user->balance->remainingFreeCredits()
            ]);
    }

    public function bulk()
    {

    }

}