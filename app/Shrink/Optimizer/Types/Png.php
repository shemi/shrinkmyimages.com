<?php

namespace App\Shrink\Optimizer\Types;

class Png extends Type
{
    protected $defoultOptions = [
        '--pngquant-allow-lossy true',
//        '--no-advpng',//long
        '--no-optipng',//long
        '--no-pngcrush',
        '--no-pngout',
        '--no-pngquant'
    ];

    protected $qualities = [
        'high' => [
            '--no-pngcrush',
            '--pngquant-speed 11',
        ],
        'best' => [
//            '--pngcrush-fix true',
            '--pngout-strategy 0',
            '--pngquant-quality 0..90',
            '--pngquant-speed 1',
            '--advpng-level 2'
        ],
        'small' => [
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