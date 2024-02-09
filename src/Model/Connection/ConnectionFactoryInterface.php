<?php

namespace KooijmanInc\Suzie\Model\Connection;

use Psr\Log\LoggerInterface;

interface ConnectionFactoryInterface
{
    /**
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $port
     * @param string $dbname
     * @param string $charset
     * @param LoggerInterface $logger
     */
    public function __construct(string $host, string $user, string $pass, string $port, string $dbname, string $charset, LoggerInterface $logger);
}