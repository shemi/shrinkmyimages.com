<?php

namespace App\Shrink\Optimizer\Types;

class Svg extends Type
{
    protected $defoultOptions = [
    ];

    protected $qualities = [

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
        return isset($this->qualities[$this->mode]) ? $this->qualities[$this->mode] : [];
    }
}