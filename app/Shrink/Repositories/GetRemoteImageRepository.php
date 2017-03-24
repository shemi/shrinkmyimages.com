<?php

namespace App\Shrink\Repositories;

class GetRemoteImageRepository
{
    private $source;
    private $saveTo;

    public function download()
    {
        $info = getimagesize($this->source);
        $mime = $info['mime'];
        $type = substr(strrchr($mime, '/'), 1);

        switch ($type) {
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

        $ext = strrchr($this->source, ".");
        $strLen = strlen($ext);
        $newName = basename(substr($this->source, 0, -$strLen)) . '.' . $newImageExt;
        $saveTo = $this->saveTo . $newName;

        return $this->loadImageCURL($saveTo);
    }

    private function loadImageCURL($save_to)
    {
        $ch = curl_init($this->source);
        $fp = fopen($save_to, "wb");

        $options = [
            CURLOPT_FILE => $fp,
            CURLOPT_HEADER => 0,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 60
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        return true;
    }
}
