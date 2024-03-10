<?php

namespace KooijmanInc\Suzie\Model\Connection;

use KooijmanInc\Suzie\Helper\Common;
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
        parent::__construct(Common::encrypt($host), Common::encrypt($user), Common::encrypt($pass), Common::encrypt($port), Common::encrypt($dbname), Common::encrypt($charset), $logger);

    }

    /**
     * @return void
     */
    public function connect(): void
    {
        parent::connect();
    }

    /**
     * @param string $database
     * @return void
     */
    public function setDatabase(string $database): void
    {
        $this->database = $database;
    }

    /**
     * @param string $query
     * @param array $binds
     * @return array
     */
    public function fetchAll(string $query, array $binds = []): array
    {
        return $this->getAll($query, $this->setCol($binds), $binds);
    }

    /**
     * @param string $query
     * @param array $binds
     * @return array
     */
    public function fetchOne(string $query, array $binds = []): array
    {
        return $this->getOne($query, $this->setCol($binds), $binds);
    }

    public function insert(string $query, array $binds = []): int
    {
        return $this->insertRow($query, $this->setCol($binds), $binds);
    }

    /**
     * @param array $bind
     * @return string
     */
    private function setCol(array $bind): string
    {
        return str_repeat('s', count($bind));
    }
}