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


    public function __construct(File $file, Shrink $shrink = null)
    {
        if($shrink) {
            $this->shrink = $shrink;
        } else {
            $this->shrink = $file->shrink;
        }

        $this->file = $file;
    }

    public function shrinkThis()
    {
        $this->optimize();
    }

    protected function optimize()
    {
        $optimizer = new Optimizer($this->file->full_path, $this->shrink->mode);

        $optimizer->optimize();

        return $this;
    }

}
