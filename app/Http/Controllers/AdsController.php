<?php

namespace App\Http\Controllers;

use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Image;
use Symfony\Component\Finder\SplFileInfo;

class AdsController extends Controller
{

    public function show()
    {
        $files = \File::allFiles(storage_path("app/public/ads"));
        $ads = collect([]);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $ads->push([
                'type' => 'image',
                'source' => url('storage/ads/' . $file->getFilename()),
                'timeout' => rand(25, 45)
            ]);
        }

        return $this->respond($ads->shuffle()->toArray());
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
