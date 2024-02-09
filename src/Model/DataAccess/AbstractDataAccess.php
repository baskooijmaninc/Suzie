<?php

namespace KooijmanInc\Suzie\Model\DataAccess;

use KooijmanInc\Suzie\Model\Connection\ConnectionFactoryInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractDataAccess implements DataAccessInterface
{
    /**
     * @var string
     */
    protected string $database;

    /**
     * @var string
     */
    protected string $table;

    /**
     * @var array
     */
    protected array $tableColumns;

    /**
     * @var bool
     */
    protected bool $debug;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var string
     */
    protected string $name;

    public function __construct(protected ConnectionFactoryInterface $connectionFactory, bool $debug = false)
    {
        $this->debug = $debug;
        $this->name = get_called_class();

        if (!empty($this->database)) {
            $this->connectionFactory->setDatabase($this->database);
        }

        $this->connectionFactory->connect();
    }

    public function getTableColumns(): iterable
    {
        if (empty($this->tableColumns)) {
            $data = $this->connectionFactory->fetchAll("SHOW COLUMNS FROM `$this->table`");

            if ($this->debug === true) {
                $this->logger->debug('Called ' . $this->name . '::get "{where}".', compact('data'));
            }

            $this->tableColumns = $data;
        }

        return $this->tableColumns;
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