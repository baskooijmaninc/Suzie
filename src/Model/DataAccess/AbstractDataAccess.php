<?php

namespace KooijmanInc\Suzie\Model\DataAccess;

abstract class AbstractDataAccess implements DataAccessInterface
{
    protected $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }
}