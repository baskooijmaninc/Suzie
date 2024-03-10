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

    /**
     * @return void
     */
    public function connect(): void;

    /**
     * @param string $database
     * @return void
     */
    public function setDatabase(string $database): void;

    /**
     * @param string $query
     * @param array $binds
     * @return array
     */
    public function fetchAll(string $query, array $binds = []): array;

    /**
     * @param string $query
     * @param array $binds
     * @return array
     */
    public function fetchOne(string $query, array $binds = []): array;

    /**
     * @param string $query
     * @param array $binds
     * @return int
     */
    public function insert(string $query, array $binds = []): int;
}