<?php

namespace KooijmanInc\Suzie\Model\DataAccess;

use Psr\Log\LoggerInterface;

interface DataAccessInterface
{
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