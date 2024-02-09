<?php

namespace KooijmanInc\Suzie;

use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;

abstract class AbstractSuzie implements SuzieInterface
{
    /**
     * @var DataAccessInterface
     */
    protected $dataAccess;

    protected $debug;

    /**
     * @param DataAccessInterface $dataAccess
     * @param bool $debug
     */
    public function __construct(DataAccessInterface $dataAccess, bool $debug = false)
    {
        $this->dataAccess = $dataAccess;
        $this->debug = $debug;
        dump($this);
    }
}