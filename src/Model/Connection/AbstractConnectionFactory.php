<?php

namespace KooijmanInc\Suzie\Model\Connection;

use Psr\Log\LoggerInterface;

abstract class AbstractConnectionFactory extends MySql implements ConnectionFactoryInterface
{
    /**
     * @var string
     */
    protected string $database;

    /**
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $port
     * @param string $dbname
     * @param string $charset
     * @param LoggerInterface $logger
     */
    public function __construct(string $host, string $user, string $pass, string $port, string $dbname, string $charset, LoggerInterface $logger)
    {
        parent::__construct($host, $user, $pass, $port, $dbname, $charset, $logger);
    }
}