<?php

namespace App\Shrink\Optimizer\Types;

abstract class Type
{
    protected $commonOptions = [];

    protected $modes = ['high', 'best', 'small'];

    protected $mode;

    public function __construct($mode = 'best')
    {
        $this->setMode($mode);
    }

    public function isValidMode($mode)
    {
        return in_array($mode, $this->modes);
    }

    /**
     * @param string $mode
     * @return Type
     * @throws \Exception
     */
    public function setMode($mode)
    {
        if(! $this->isValidMode($mode)) {
            throw new \Exception("unknown mode");
        }

        $this->mode = $mode;

        return $this;
    }

    /**
     * @return array
     */
    protected abstract function getDefoultOptions();

    /**
     * @return array
     */
    protected abstract function getModeOptions();

    /**
     * @return array
     */
    public function getOptions()
    {
        return array_merge(
            $this->commonOptions,
            $this->getDefoultOptions(),
            $this->getModeOptions()
        );
    }

}