<?php

namespace App\Http\Controllers\Api;

use App\File;
use Hashids;
use Illuminate\Http\Request;

class DownloadController extends ApiController
{

    public function download($shrinkFileId, $fileName, Request $request)
    {
        $user = auth()->user();

        try {
            $ids = Hashids::connection('shrinkFile')->decode($shrinkFileId);
            $shrinkId = $ids[0];
            $fileId = $ids[1];
        } catch (\Exception $e) {
            return $this->respondNotFound();
        }

        $file = File::where('shrink_id', $shrinkId)
            ->where('id', $fileId)
            ->first();

        if(! $file) {
            return $this->respondNotFound();
        }

        if ($file->shrink->user_id != $user->id) {
            return $this->respondNotAuthorized();
        }

        $filePath = base_path("storage/app/{$file->path}");

        if(! is_file($filePath)) {
            return $this->respondNotFound();
        }

        $this->createCallModel($request, $user, $shrinkId, 0, 'download');

        return response()->download($filePath, $file->name);
    }

}