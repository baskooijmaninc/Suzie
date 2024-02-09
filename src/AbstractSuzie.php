<?php

namespace KooijmanInc\Suzie;

use KooijmanInc\Suzie\DataMapper\DataMapperInterface;
use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractSuzie implements SuzieInterface
{
    /**
     * @var DataAccessInterface
     */
    protected DataAccessInterface $dataAccess;

    /**
     * @var DataMapperInterface
     */
    protected DataMapperInterface $dataMapper;

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
     * @var Stopwatch
     */
    protected Stopwatch $stopwatch;

    /**
     * @var iterable
     */
    protected iterable $tableColumns;

    protected $form;

    protected $entity;

    /**
     * @param DataAccessInterface $dataAccess
     * @param DataMapperInterface $dataMapper
     * @param LoggerInterface|null $logger
     * @param bool $debug
     */
    public function __construct(DataAccessInterface $dataAccess, DataMapperInterface $dataMapper, LoggerInterface $logger = null, bool $debug = false)
    {
        $this->dataAccess = $dataAccess;
        $this->dataMapper = $dataMapper;
        $this->logger = $logger;
        $this->debug = $debug;

        $this->name = get_called_class();

        $this->dataAccess->setLogger($this->logger);
        $this->dataAccess->setDebug($this->debug);

        $this->dataMapper->setSuzie($this);
    }

    /**
     * @return $this
     */
    #[Required]
    public function create(array $data = []): static
    {
        $requestId = uniqid();

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::create {requestId}', compact('requestId', 'data'));
        }

        if ($this->debug === true) {
            $e = $this->stopwatch->start($this->name . '::create#' . $requestId, 'suzie');
        }

        if ($data === []) {
            if (empty($this->tableColumns)) {
                $this->tableColumns = $this->dataAccess->getTableColumns();
            }
        }

        $this->form = $this->dataMapper->rowToFormBuilder($this->tableColumns);
        $this->entity = $this->dataMapper->rowToEntity($this->tableColType('emptyEntity', (array)$this->tableColumns));

        if (isset($e) && $e->isStarted()) {
            $e->stop();
        }

        return $this;
    }

    /**
     * @param Stopwatch $stopwatch
     * @return SuzieInterface
     */
    public function setStopwatch(Stopwatch $stopwatch): SuzieInterface
    {
        $this->stopwatch = $stopwatch;

        return $this;
    }

    /**
     * @param bool $debug
     * @return SuzieInterface
     */
    public function setDebug(bool $debug): SuzieInterface
    {
        $this->debug = $debug;

        return $this;
    }

    private function tableColType(string $type, array $columns): array
    {
        if ($type === 'emptyEntity') {
            foreach ($columns as $column) {
                if (isset($column['Field'])) {
                    if ($column['Default'] === "UNIXTIMESTAMP") {
                        $column['Default'] = time();
                    }
                    if ($column['Key'] === 'PRI' && $column['Field'] !== 'id') {

                        $tableColumns['id'] = $column['Default'];
                        $tableColumns['customId'] = $column['Field'];
                    } else {
                        $tableColumns[$column['Field']] = $column['Default'];
                    }
                }
            }
        }

        return $tableColumns ?? [];
    }
}