<?php

namespace App\Http\Controllers;

use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Image;

class AdsController extends Controller
{

    public function show()
    {

        return $this->respond([
            [
                'type' => 'image',
                'source' => url('storage/ads/ad1.jpg'),
                'timeout' => 30
            ],
            [
                'type' => 'image',
                'source' => url('storage/ads/ad2.jpg'),
                'timeout' => 30
            ]
        ]);
    }

//    public function serveAdAsset($name)
//    {
//        $path = storage_path("app/ads/{$name}");
//
//        return Cache::remember($path, Carbon::now()->addHour(), function () use ($path) {
//            $image = Image::make($path);
//
//            $image->resize(1920, 1020, function ($constraint) {
//                $constraint->aspectRatio();
//                $constraint->upsize();
//            });
//
//            return $image->response('jpg', 75);
//        });
//    }

}
