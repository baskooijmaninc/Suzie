<?php

namespace KooijmanInc\Suzie\Model\DataAccess;

use KooijmanInc\Suzie\Model\Connection\ConnectionFactoryInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractDataAccess implements DataAccessInterface
{
    protected $database;

    protected $debug;

    public function __construct(ConnectionFactoryInterface $connectionFactory, bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function setLogger(LoggerInterface $logger = null): DataAccessInterface
    {
        $this->logger = $logger;

        return $this;
    }

    public function setDebug(bool $debug): DataAccessInterface
    {
        $this->debug = $debug;

        return $this;
    }
}