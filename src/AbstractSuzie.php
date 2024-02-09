<?php

namespace KooijmanInc\Suzie;

use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractSuzie implements SuzieInterface
{
    /**
     * @var DataAccessInterface
     */
    protected DataAccessInterface $dataAccess;

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

    /**
     * @param DataAccessInterface $dataAccess
     * @param LoggerInterface|null $logger
     * @param bool $debug
     */
    public function __construct(DataAccessInterface $dataAccess, LoggerInterface $logger = null, bool $debug = false)
    {
        $this->dataAccess = $dataAccess;
        $this->logger = $logger;
        $this->debug = $debug;

        $this->name = get_called_class();

        $this->dataAccess->setLogger($this->logger);
        $this->dataAccess->setDebug($this->debug);
    }
}