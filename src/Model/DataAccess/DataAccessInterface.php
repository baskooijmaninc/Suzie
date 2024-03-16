<?php

namespace KooijmanInc\Suzie\Model\DataAccess;

use Psr\Log\LoggerInterface;

interface DataAccessInterface
{
    /**
     * @return iterable
     */
    public function getTableColumns(): iterable;

    public function get(string $where = null, array $bind = [], $onlyFirstRow = false): iterable;

    public function getBy(array $rules, string $where = null, array $bind = [], bool $onlyFirstRow = false): iterable;

    /**
     * @param array $fields
     * @return int|bool
     */
    public function insert(array $fields = []): int|bool;

    /**
     * @param LoggerInterface|null $logger
     * @return DataAccessInterface
     */
    public function setLogger(LoggerInterface $logger = null): DataAccessInterface;

    /**
     * @param bool $debug
     * @return DataAccessInterface
     */
    public function setDebug(bool $debug): DataAccessInterface;
}