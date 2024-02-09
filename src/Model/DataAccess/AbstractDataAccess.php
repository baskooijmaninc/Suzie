<?php

namespace KooijmanInc\Suzie\Model\DataAccess;

use KooijmanInc\Suzie\Model\Connection\ConnectionFactoryInterface;

abstract class AbstractDataAccess implements DataAccessInterface
{
    protected $debug;

    public function __construct(ConnectionFactoryInterface $connectionFactory, bool $debug = false)
    {
        $this->debug = $debug;
    }
}