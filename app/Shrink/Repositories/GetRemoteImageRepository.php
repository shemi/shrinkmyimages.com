<?php

namespace App\Shrink\Repositories;

use App\Shrink\Exceptions\RemoteFileHttpErrorException;
use App\Shrink\Exceptions\RemoteFileNotImageException;
use Illuminate\Http\UploadedFile;

class GetRemoteImageRepository
{
    private $source;
    private $tempFilePath;
    private $imageInfo;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function download()
    {
        $this->createTempFile();
        $this->loadImageCURL();
        $this->setImageInfo();

        switch ($this->getClientMimeType()) {
            case 'jpeg':
                $newImageExt = 'jpg';
                break;
            case 'png':
                $newImageExt = 'png';
                break;
            case 'gif':
                $newImageExt = 'gif';
                break;
            default:
                $newImageExt = 'jpg';
        }

        $clientFileName = pathinfo($this->source, PATHINFO_FILENAME) . '.' . $newImageExt;

        return new UploadedFile(
            $this->tempFilePath,
            $clientFileName,
            $this->getClientMimeType(),
            filesize($this->tempFilePath),
            0,
            true
        );
    }

    private function setImageInfo()
    {
        $this->imageInfo = getimagesize($this->tempFilePath);
    }

    private function createTempFile()
    {
        $this->tempFilePath = tempnam(sys_get_temp_dir(), uniqid('smi', true));
    }

    private function getClientMimeType()
    {
        return $this->imageInfo['mime'];
    }

    public function deleteTempFile()
    {
        if(file_exists($this->tempFilePath)) {
            unlink($this->tempFilePath);
        }
    }

    private function getClientFileName()
    {
        return basename($this->source);
    }

    private function loadImageCURL()
    {
        $ch = curl_init($this->source);
        $fp = fopen($this->tempFilePath, "wb");

        $options = [
            CURLOPT_FILE => $fp,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 60
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);
        fclose($fp);

        $hasHttpError = ($httpCode < 200 || $httpCode >= 300);
        $isNoteImage = (strchr($contentType, 'image') === false);

        if($hasHttpError || $isNoteImage) {
            $this->deleteTempFile();

            if($hasHttpError) {
                throw new RemoteFileHttpErrorException("Http error", $httpCode);
            }

            if($isNoteImage) {
                throw new RemoteFileNotImageException("not image", $httpCode);
            }
        }

        return true;
    }

}
