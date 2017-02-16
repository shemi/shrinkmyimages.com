<?php

namespace App\Shrink\Optimizer\Types;

class Jpeg extends Type
{
    protected $defoultOptions = [
        '--jpegoptim-allow-lossy true',
        '--jpegoptim-strip all',
        '--jpegrecompress-allow-lossy true',
        '--jpegtran-copy-chunks true'
    ];

    protected $qualities = [
        'high' => [
            '--jpegoptim-max-quality 95',
            '--jpegrecompress-quality 3'
        ],
        'best' => [
            '--jpegoptim-max-quality 50',
            '--jpegrecompress-quality 1'
        ],
        'small' => [
            '--jpegoptim-max-quality 48',
            '--jpegrecompress-quality 1'
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