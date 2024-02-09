<?php

namespace KooijmanInc\Suzie\Model\Connection;

use Psr\Log\LoggerInterface;

class MySql
{
    /**
     * @var mysqli
     */
    private mysqli $conn;

    /**
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $port
     * @param string $dbname
     * @param string $charset
     * @param LoggerInterface $logger
     */
    public function __construct(private readonly string $host, private readonly string $user, private readonly string $pass, private readonly string $port, private readonly string $dbname, private readonly string $charset, private LoggerInterface $logger)
    {

    }
}