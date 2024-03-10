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

    public function get(string $where = null, array $bind = [], $onlyFirstRow = false): iterable
    {
        $where = (!empty($where)) ? ' WHERE ' . $where : null;
        $query = "SELECT * FROM `{$this->table}`{$where}";

        if ($onlyFirstRow) {
            $data = $this->connectionFactory->fetchOne($query, $bind);
        } else {
            $data = $this->connectionFactory->fetchAll($query, $bind);
        }

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::get "{where}".', compact('where', 'bind', 'onlyFirstRow', 'query', 'data'));
        }

        return $data;
    }

    /**
     * @param array $fields
     * @return int|bool
     */
    public function insert(array $fields = []): int|bool
    {
        $query = "INSERT INTO `{$this->table}` (`" . implode('`, `', array_keys($fields)) . "`) VALUES (" . rtrim(str_repeat('?,', count($fields)), ',').")";

        $return = $this->connectionFactory->insert($query, array_values($fields));

        if ($this->debug === true && $this->logger !== null) {
            $this->logger->debug('Called ' . $this->name . '::insert.', compact('fields', 'query', 'return'));
        }

        return $return;
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