<?php

namespace App\Http\Controllers;

use App\File;
use App\Shrink;
use Zipper;

class DownloadController extends Controller
{
    public function download($shrinkId, $fileId = null)
    {
        if ($fileId) {
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

        if ($shrink->files->count() <= 1) {
            return $this->downloadSingle($shrink->files->first());
        }

        $folder = "storage/app/{$shrink->folder_path}";
        $zipName = "We_shrink_your_images.zip";
        $zipFullPath = base_path("{$folder}/{$zipName}");

        if (!file_exists($zipFullPath)) {
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
        if ($id instanceof File) {
            $file = $id;
        } else {
            $file = File::where('id', $id)
                ->where('shrink_id', $shrinkId)
                ->firstOrFail();
        }

        $filePath = base_path("storage/app/{$file->path}");

        if (!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath, $file->name);
    }
}