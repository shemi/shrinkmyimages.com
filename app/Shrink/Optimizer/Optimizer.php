<?php

namespace App\Shrink\Optimizer;

use App\Shrink\Exceptions\OptimizerFailException;
use App\Shrink\Optimizer\Types\Gif;
use App\Shrink\Optimizer\Types\Jpeg;
use App\Shrink\Optimizer\Types\Png;
use App\Shrink\Optimizer\Types\Svg;
use App\Shrink\Optimizer\Types\Type;

class Optimizer
{
    protected $command = "image_optim";

    protected $globalOptions = [
        '--no-progress',
//        '--no-pngout',
//        'threads 2',
        '--allow-lossy',
//        '--nice 10',
//        '--no-nice'
    ];

    protected $appendToEnd = [
        '2>&1'
    ];

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var string
     */
    protected $imagePath;

    /**
     * @var string
     */
    protected $mode;

    public function __construct($imagePath, $mode)
    {
        $this->setImagePath($imagePath);
        $this->mode = $mode;
        $this->setType();
    }

    public function setImagePath($image)
    {
        if(! file_exists($image)) {
            throw new \Exception('cant find file: ' . $image);
        }

        $this->imagePath = $image;
    }

    /**
     * @return string
     */
    protected function getImageType()
    {
        return strtolower(pathinfo($this->imagePath, PATHINFO_EXTENSION));
    }

    protected function setType()
    {
        switch ($this->getImageType()) {
            case 'png':
                $this->type = new Png($this->mode);
                break;
            case 'jpeg':
            case 'jpg':
                $this->type = new Jpeg($this->mode);
                break;
            case 'gif':
                $this->type = new Gif($this->mode);
                break;
            case 'svg':
                $this->type = new Svg($this->mode);
                break;
        }
    }

    /**
     * @return string
     */
    protected function buildCommend()
    {
        if($this->getImageType() == 'svg') {
            return "svgo -q " . escapeshellarg($this->imagePath);
        }

        $command = $this->globalOptions;
        $command = array_prepend($command, $this->command);

        $command = array_merge(
            $command,
            $this->type->getOptions(),
            [escapeshellarg($this->imagePath)],
            $this->appendToEnd
        );

        return implode(' ', $command);
    }

    public function optimize()
    {
        $commend = $this->buildCommend();
        logger('execute: ' . $commend);

        $log = exec($commend, $aOutput, $iResult);

        logger('commend output: ' . json_encode($aOutput));

        if ($iResult !== 0) {
            throw new OptimizerFailException($iResult, $commend, $this->imagePath, $log, $aOutput);
        }

        return true;
    }

}