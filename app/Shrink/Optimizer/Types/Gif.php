<?php

namespace App\Shrink\Optimizer\Types;

class Gif extends Type
{
    protected $defoultOptions = [
        '--gifsicle-careful true'
    ];

    protected $qualities = [
        'high' => [
            '--gifsicle-level 1',
        ],
        'best' => [
            '--gifsicle-level 3',
        ],
        'small' => [
            '--gifsicle-level 3',
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