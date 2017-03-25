<?php

namespace App\Http\Controllers;

use App\File;
use App\Shrink;
use App\Shrink\Repositories\ShrinkRepository;
use App\Shrink\Repositories\UploadRepository;
use App\Shrink\Shrinker;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShrinkController extends Controller
{

    private function getDownloadUrl($shrinkId, $fileId = null)
    {
        $base = url("shrink/{$shrinkId}/download");

        return $fileId ? $base . '/' . $fileId : $base;
    }

    public function show($id)
    {
        $shrink = Shrink::where('id', $id)
            ->with('files')
            ->firstOrFail();

        return $this->respond([
            'id' => $shrink->id,
            'downloadLink' => $this->getDownloadUrl($shrink->id),
            'beforeTotalSize' => $shrink->before_total_size,
            'afterTotalSize' => $shrink->after_total_size,
            'percent' => 100 - round(($shrink->after_total_size / $shrink->before_total_size) * 100, 2)
        ]);
    }

    public function create(Request $request, ShrinkRepository $shrinkRepository)
    {
        $user = Auth::user();
        $shrinkRepository->validateCreateRequest($request);
        $shrink = $shrinkRepository->create($request, $user);

        return $this->respond([
            'id' => $shrink->id
        ]);
    }

    public function upload($shrinkId, Request $request, UploadRepository $uploadRepo)
    {
        $user = Auth::user();

        $uploadRepo->validateUploadRequest($request);
        $shrink = Shrink::where('id', $shrinkId)->firstOrFail();
        $uploadRepo->upload($request->file('image'), $shrink);
        $file = $uploadRepo->getFile();

        if($user && ! $shrink->user_id) {
            $shrink->user_id = $user->id;
            $shrink->save();
        }

        return $this->respond([
            'downloadUrl' => $this->getDownloadUrl($file->shrink_id, $file->id),
            'fileId' => $file->id,
            'afterSize' => $file->size_after,
            'percent' => $file->reduced_percentage
        ]);
    }

}
