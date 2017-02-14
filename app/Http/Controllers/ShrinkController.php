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

    public function show($id)
    {
        $shrink = Shrink::where('id', $id)
            ->with('files')
            ->firstOrFail();

        $folder = "storage/app/{$shrink->folder_path}";
        $zipName = "We_shrink_your_images.zip";
        $zipFullPath = "{$folder}/{$zipName}";

        if(! file_exists(base_path($zipFullPath))) {
            $files = glob(base_path("{$folder}/*"));
            Zipper::make(base_path($zipFullPath))->add($files)->close();
        }

        return $this->respond([
            'id' => $shrink->id,
            'downloadLink' => url("shrink/{$shrink->id}/download"),
            'beforeTotalSize' => $shrink->before_total_size,
            'afterTotalSize' => $shrink->after_total_size
        ]);
    }

    public function download($id)
    {
        $shrink = Shrink::where('id', $id)
            ->firstOrFail();

        $folder = "storage/app/{$shrink->folder_path}";
        $zipName = "We_shrink_your_images.zip";
        $zipFullPath = base_path("{$folder}/{$zipName}");

        if(! file_exists($zipFullPath)) {
            abort(404);
        }

        return response()->download($zipFullPath);
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
            'afterSize' => $file->size_after,
            'percent' => round(($file->size_after / $file->size_before) * 100, 2)
        ]);
    }

}
