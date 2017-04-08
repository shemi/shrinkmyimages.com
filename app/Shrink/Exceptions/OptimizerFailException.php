<?php

namespace App\Shrink\Exceptions;

use Exception;

class OptimizerFailException extends Exception
{

    /**
     * @var string
     */
    private $commandResult;

    /**
     * @var int
     */
    private $commend;

    /**
     * @var Exception
     */
    private $imagePath;

    /**
     * @var string
     */
    private $commandLog;

    /**
     * @var array
     */
    private $commandOutput;

    /**
     * OptimizerFailException constructor.
     * @param integer $commandResult
     * @param string $commend
     * @param string $imagePath
     * @param string $commandLog
     * @param array $commandOutput
     */
    public function __construct($commandResult, $commend, $imagePath, $commandLog, $commandOutput)
    {
        parent::__construct("image_optim command was unable to optimise {$imagePath}");

        $this->commandResult = $commandResult;
        $this->commend = $commend;
        $this->imagePath = $imagePath;
        $this->commandLog = $commandLog;
        $this->commandOutput = $commandOutput;
    }

    /**
     * @return string
     */
    public function getCommandResult()
    {
        return $this->commandResult;
    }

    /**
     * @return int
     */
    public function getCommend()
    {
        return $this->commend;
    }

    /**
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }

    /**
     * @return mixed
     */
    public function getCommandLog()
    {
        return $this->commandLog;
    }

    /**
     * @return array
     */
    public function getCommandOutput()
    {
        return $this->commandOutput;
    }


}