<?php

namespace App\Http\Controllers;

use App\File;
use App\Shrink;
use App\Shrink\Shrinker;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Zipper;

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

    public function download($shrinkId, $fileId = null)
    {
        if($fileId) {
            return $this->downloadSingle($fileId, $shrinkId);
        } else {
            return $this->downloadAll($shrinkId);
        }
    }

    public function downloadAll($shrinkId)
    {
        $shrink = Shrink::where('id', $shrinkId)
            ->with('files')
            ->firstOrFail();

        if($shrink->files->count() <= 1) {
            return $this->downloadSingle($shrink->files->first());
        }

        $folder = "storage/app/{$shrink->folder_path}";
        $zipName = "We_shrink_your_images.zip";
        $zipFullPath = base_path("{$folder}/{$zipName}");

        if(! file_exists($zipFullPath)) {
            $zip = Zipper::make($zipFullPath);

            foreach ($shrink->files as $file) {
                $zip->add(base_path("{$folder}/{$file->md5_name}"), $file->name);
            }

            $zip->close();
        }

        return response()->download($zipFullPath);
    }

    public function downloadSingle($id, $shrinkId = null)
    {
        if($id instanceof File) {
            $file = $id;
        } else {
            $file = File::where('id', $id)
                ->where('shrink_id', $shrinkId)
                ->firstOrFail();
        }

        $filePath = base_path("storage/app/{$file->path}");

        if(! file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $file->name);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $shrink = new Shrink();
        $expire = Carbon::now()
            ->copy()
            ->addDay();

        $this->validate($request, [
            'mode' => [
                'required',
                Rule::in(['high', 'best', 'small'])
            ],
            'width' => 'nullable|integer',
            'height' => 'nullable|integer'
        ]);

        if($user) {
            $shrink->user_id = $user->id;
        }

        $shrink->expire_at = $expire;
        $shrink->mode = $request->input('mode');
        $shrink->max_width = $request->input('width');
        $shrink->max_height = $request->input('height');


        $shrink->save();

        return $this->respond([
           'id' => $shrink->id
        ]);
    }

    public function upload($shrinkId, Request $request)
    {
        $shrink = Shrink::where('id', $shrinkId)->firstOrFail();

        $this->validate($request, [
            'image' => 'required|image|max:15000'
        ]);

        $upload = $request->file('image');
        $beforeSize = $upload->getSize();
        $shrink->before_total_size += $beforeSize;

        if(! $upload->isValid()) {
            abort(422);
        }

        $file = new File();

        $file->shrink_id = $shrink->id;
        $file->name = $upload->getClientOriginalName();
        $file->ext = $upload->extension();
        $file->md5_name = $upload->hashName();
        $file->size_before = $beforeSize;

        $upload->storeAs($file->directory, $file->md5_name);

        $shrinker = new Shrinker($file);
        $shrinker->shrinkThis();

        $shrink->after_total_size += $file->size_after;

        $file->save();
        $shrink->save();

        return $this->respond([
            'downloadUrl' => $this->getDownloadUrl($file->shrink_id, $file->id),
            'fileId' => $file->id,
            'afterSize' => $file->size_after,
            'percent' => 100 - round(($file->size_after / $file->size_before) * 100, 2)
        ]);
    }

}
