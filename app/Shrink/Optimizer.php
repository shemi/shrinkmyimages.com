<?php

namespace App\Shrink;

use App\File;
use App\Shrink;
use PHPImageOptim\PHPImageOptim;
use PHPImageOptim\Tools\Jpeg\JpegOptim;
use PHPImageOptim\Tools\Jpeg\JpegTran;
use PHPImageOptim\Tools\Png\OptiPng;
use PHPImageOptim\Tools\Png\PngCrush;
use PHPImageOptim\Tools\ToolsInterface;

class Optimizer_old
{
    protected $optimizers = [
        AdvPng::class => '/usr/bin/advpng',
        OptiPng::class => '/usr/bin/optipng',
        PngCrush::class => '/usr/bin/pngcrush',
        JpegOptim::class => '/usr/bin/jpegoptim',
        JpegTran::class => '/opt/mozjpeg/bin/cjpeg'
    ];

    protected $mods = [
        'high' => [
            AdvPng::class => ToolsInterface::OPTIMISATION_LEVEL_BASIC,
            OptiPng::class => ToolsInterface::OPTIMISATION_LEVEL_BASIC,
            PngCrush::class => ToolsInterface::OPTIMISATION_LEVEL_BASIC,
            JpegOptim::class => ToolsInterface::OPTIMISATION_LEVEL_BASIC,
            JpegTran::class => ToolsInterface::OPTIMISATION_LEVEL_BASIC
        ],
        'best' => [
            AdvPng::class => ToolsInterface::OPTIMISATION_LEVEL_STANDARD,
            OptiPng::class => ToolsInterface::OPTIMISATION_LEVEL_STANDARD,
            PngCrush::class => ToolsInterface::OPTIMISATION_LEVEL_STANDARD,
            JpegOptim::class => ToolsInterface::OPTIMISATION_LEVEL_STANDARD,
            JpegTran::class => ToolsInterface::OPTIMISATION_LEVEL_STANDARD
        ],
        'small' => [
            AdvPng::class => ToolsInterface::OPTIMISATION_LEVEL_EXTREME,
            OptiPng::class => ToolsInterface::OPTIMISATION_LEVEL_EXTREME,
            PngCrush::class => ToolsInterface::OPTIMISATION_LEVEL_EXTREME,
            JpegOptim::class => ToolsInterface::OPTIMISATION_LEVEL_EXTREME,
            JpegTran::class => ToolsInterface::OPTIMISATION_LEVEL_EXTREME
        ],
    ];

    /**
     * @var ToolsInterface
     */
    protected $advPng;

    /**
     * @var ToolsInterface
     */
    protected $optiPng;

    /**
     * @var ToolsInterface
     */
    protected $pngCrush;

    /**
     * @var ToolsInterface
     */
    protected $jpegOptim;

    /**
     * @var ToolsInterface
     */
    protected $jpegTran;

    private $filePath;
    private $mode;

    public function __construct($filePath, $mode)
    {
        $this->filePath = $filePath;
        $this->mode = $mode;

        $this->setOptimizers();
    }

    private function setOptimizers()
    {
        foreach ($this->optimizers as $class => $binary)
        {
            $attribute = camel_case(class_basename($class));
            $this->$attribute = new $class();
            $this->$attribute->setBinaryPath($binary);
            $this->$attribute->setOptimisationLevel($this->getOptimisationLevel($class));
        }
    }

    private function getOptimisationLevel($class)
    {
        return $this->mods[$this->mode][$class];
    }

    public function optimiseJpeg()
    {
        $optimizer = new PHPImageOptim();

        $optimizer->setImage($this->filePath);

        $optimizer
            ->chainCommand($this->jpegOptim)
            ->chainCommand($this->jpegTran);

        $optimizer->optimise();

        return true;
    }

    public function optimisePng()
    {
        $optimizer = new PHPImageOptim();

        $optimizer->setImage($this->filePath);

        $optimizer
            ->chainCommand($this->advPng);
//            ->chainCommand($this->optiPng);
//            ->chainCommand($this->pngCrush);

        $optimizer->optimise();

        return true;
    }

}