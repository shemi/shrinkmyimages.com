<?php

namespace App\Shrink\Optimizer\Types;

class Png extends Type
{
    protected $defoultOptions = [
        '--pngquant-allow-lossy true',
        '--no-advpng',//long
        '--no-optipng',//long
    ];

    protected $qualities = [
        'high' => [
//            '--advpng-level 1',
//            '--optipng-level 2',
//            '--pngquant-quality 100..100',
            '--no-pngcrush',
            '--pngquant-speed 11',
        ],
        'best' => [
//            '--advpng-level 2',
//            '--optipng-level 4',
//            '--pngquant-quality 50..100',
            '--no-pngcrush',
            '--pngquant-speed 3',
        ],
        'small' => [
//            '--advpng-level 3',
//            '--optipng-level 6',
            '--pngquant-speed 1',
        ]
    ];

    /**
     * @return array
     */
    protected function getDefoultOptions()
    {
        return $this->defoultOptions;
    }

    /**
     * @return array
     */
    protected function getModeOptions()
    {
        return $this->qualities[$this->mode];
    }
}