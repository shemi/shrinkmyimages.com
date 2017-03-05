<?php

namespace App\Shrink;

use App\File;
use App\Shrink;
use App\Shrink\Optimizer\Optimizer;
use Image;
use File as StorageFile;

class Shrinker
{

    protected $qualities = [
        'high' => 95,
        'best' => 75,
        'small' => 65
    ];

    /**
     * @var Shrink
     */
    protected $shrink;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var \Intervention\Image\Image
     */
    protected $image;


    public function __construct(File &$file, Shrink $shrink = null)
    {
        if($shrink) {
            $this->shrink = $shrink;
        } else {
            $this->shrink = $file->shrink;
        }

        $this->file = $file;
        $this->image = Image::make($this->getFileFullPath());
    }

    public function shrinkThis()
    {
        $this->optimize()
            ->updateModel();
    }

    protected function resize()
    {
        $toWith = $this->shrink->max_width;
        $toHeight = $this->shrink->max_height;

        if (!$toHeight && !$toWith) {
            return $this;
        }

        $this->image->resize($toWith, $toHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $this;
    }

    protected function getShrinkQuality()
    {
        return $this->qualities[$this->shrink->mode];
    }

    protected function getFileFullPath()
    {
        return storage_path("app/{$this->file->path}");
    }

    protected function optimize()
    {
        $optimizer = new Optimizer($this->getFileFullPath(), $this->shrink->mode);

        $optimizer->optimize();

        return $this;
    }

    protected function save()
    {
        $this->image->save(
            $this->getFileFullPath(),
            $this->getShrinkQuality()
        );

        return $this;
    }

    protected function updateModel()
    {
        $this->file->size_after = StorageFile::size($this->getFileFullPath());
    }

}
