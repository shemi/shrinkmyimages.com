<?php

namespace App\Shrink\Repositories;

use App\File;
use App\Shrink;
use App\Shrink\Shrinker;
use Carbon\Carbon;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class UploadRepository
{
    use ValidatesRequests;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Shrink
     */
    private $shrink;

    /**
     * @var UploadedFile
     */
    private $upload;

    /**
     * @var int
     */
    private $beforeSize;


    public function upload(Request $request, Shrink $shrink)
    {

        $this
            ->setShrink($shrink)
            ->setUpload($request->file('image'))
            ->setBeforeSize($this->upload->getSize())
            ->updateShrinkBeforeTotalSize()
            ->createFileModel()
            ->storeFileInShrinkFolder()
            ->shrinkTheImage()
            ->updateShrinkAfterTotalSize()
            ->saveModels();

    }

    /**
     * @return $this
     */
    public function updateShrinkBeforeTotalSize()
    {
        $this->shrink->before_total_size += $this->beforeSize;

        return $this;
    }

    /**
     * @return $this
     */
    public function updateShrinkAfterTotalSize()
    {
        $this->shrink->after_total_size += $this->file->size_after;

        return $this;
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setBeforeSize(int $size)
    {
        $this->beforeSize = $size;

        return $this;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return $this
     */
    public function setUpload(UploadedFile $uploadedFile)
    {
        $this->upload = $uploadedFile;

        return $this;
    }

    /**
     * @param Shrink $shrink
     * @return $this
     */
    public function setShrink(Shrink $shrink)
    {
        $this->shrink = $shrink;

        return $this;
    }

    /**
     * @return $this
     */
    public function createFileModel()
    {
        $this->file = new File();

        $this->file->shrink_id = $this->shrink->id;
        $this->file->name = $this->upload->getClientOriginalName();
        $this->file->ext = $this->upload->extension();
        $this->file->md5_name = $this->upload->hashName();
        $this->file->size_before = $this->beforeSize;

        return $this;
    }

    /**
     * @return $this
     */
    public function storeFileInShrinkFolder()
    {
        $this->upload->storeAs($this->file->directory, $this->file->md5_name);

        return $this;
    }

    /**
     * @return $this
     */
    public function shrinkTheImage()
    {
        $shrinker = new Shrinker($this->file, $this->shrink);
        $shrinker->shrinkThis();

        return $this;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function validateUploadRequest(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|max:15000'
        ]);

        return $this;
    }

    /**
     * @return $this
     */
    public function saveModels()
    {
        $this->shrink->save();
        $this->file->save();

        return $this;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

}