<?php

namespace KooijmanInc\Suzie;

use Exception;
use KooijmanInc\Suzie\DataMapper\DataMapperInterface;
use KooijmanInc\Suzie\FormBuilder\FormBuilderInterface;
use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;
use KooijmanInc\Suzie\Model\Entity\EntityInterface;
use KooijmanInc\Suzie\Object\FormObject\ObjectInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractSuzie implements SuzieInterface
{
    /**
     * @var array
     */
    protected static array $instance = [];

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

    protected array $belongsTo = [];

    protected array $hasOne = [];

    /**
     * @var FormBuilderInterface
     */
    public $formBuilder;

    public $entity;

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
        $this->dataMapper->setDataAccess($this->dataAccess);

        self::$instance[$this->name] = $this;
    }

    public function getOne(...$rules): ?EntityInterface
    {
        $requestId = uniqid();

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::create {requestId}', compact('requestId', 'rules'));
        }

        if (count($rules) === 1 && isset($rules[0]) && is_int($rules[0])) {
            dump("rules has 1: ", $rules);
        } elseif ((count($rules) === 1 && isset($rules[0]) && !is_array($rules[0]) && !is_int($rules[0])) ) {
            dump("between: ", $rules);
        } else {
            if (count($rules) !== 0 && !is_array($rules[0])) {
                dump('here');
            }

            $row = $this->dataAccess->getBy($rules[0] ?? [], $rules[1] ?? null, $rules[2] ?? [], true);
            $return = empty($row) ? null : $this->dataMapper->rowToEntity($row);
        }

        if ($this->debug === true) {
            $e = $this->stopwatch->start($this->name . '::create#' . $requestId, 'suzie');
        }

        if (isset($e) && $e->isStarted()) {
            $e->stop();
        }

        return $return ?? null;
    }

    /**
     * @param FormBuilderInterface $formBuilder
     * @return array|bool
     */
    public function hasRecord(FormBuilderInterface $formBuilder): array|bool
    {
        //dump($formBuilder);
        $rules = $formBuilder->getRules();
        //dump($rules);
        $requestId = uniqid();

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::create {requestId}', compact('requestId', 'formBuilder'));
        }

        if ($this->debug === true) {
            $e = $this->stopwatch->start($this->name . '::create#' . $requestId, 'suzie');
        }

        if ($rules !== false) {
            $result = $this->dataAccess->get($rules[0] ?? null, $rules[1] ?? null, true);
            $return = [(bool)$result, $result];
        }

        if (isset($e) && $e->isStarted()) {
            $e->stop();
        }

        return $return ?? false;
    }

    public function save(EntityInterface &$entity, bool $validate = true): bool
    {
        $requestId = uniqid();

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::save {requestId}', compact('requestId', 'entity'));
        }

        if ($this->debug === true) {
            $e = $this->stopwatch->start($this->name . '::save#' . $requestId, 'suzie');
        }

        if ($this->checkEntityType($entity instanceof ObjectInterface ? $entity->getObject() : $entity) === true) {
            if ($entity->hasUnsavedChanges() === false) {
                $return = true;
            } else {
                if ($validate === false) {
                    if ($entity->id !== null && $entity->id !== 'auto_increment') {
                        dump('to update?');
                    } else {
                        //dump($entity, $validate);
                        $return = $this->insert($entity);
                    }
                }
            }
        } else {
            dump($entity);
            throw new Exception("Suzie {$this->name}::save {requestId} failed the entity type check");
        }


        if (isset($e) && $e->isStarted()) {
            $e->stop();
        }

        return $return ?? false;
    }

    public function saveForm(ObjectInterface &$formElements, bool $validate = true): bool
    {
        dump($formElements);
        $requestId = uniqid();

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::save {requestId}', compact('requestId', 'formElements'));
        }

        if ($this->debug === true) {
            $e = $this->stopwatch->start($this->name . '::save#' . $requestId, 'suzie');
        }



        if (isset($e) && $e->isStarted()) {
            $e->stop();
        }

        return false;
    }

    public function saveFormAll(ObjectInterface &$formElements, bool $validate = true, array &$argv = []): bool
    {
        dump($formElements);
        $requestId = uniqid();

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::save {requestId}', compact('requestId', 'formElements'));
        }

        if ($this->debug === true) {
            $e = $this->stopwatch->start($this->name . '::save#' . $requestId, 'suzie');
        }



        if (isset($e) && $e->isStarted()) {
            $e->stop();
        }

        return false;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function checkEntityType($entity): bool
    {
        return $this->dataMapper->checkEntityType($entity);
    }

    /**
     * @param array $data
     * @return AbstractSuzie
     */
    #[Required]
    public function create(array $data = []): AbstractSuzie
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

        $this->formBuilder = $this->dataMapper->rowToFormBuilder($this->tableColumns, $this->tableColType('emptyEntity', (array)$this->tableColumns));
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

    public static function getInstance(): AbstractSuzie
    {
        $name = get_called_class();

        if (!isset(self::$instance[$name])) {
            SuzieFactory::create($name);
        }

        return self::$instance[$name];
    }

    protected function insert(EntityInterface $entity): bool
    {
        $requestId = uniqid();

        if ($this->debug === true) {
            $this->logger->debug('Called ' . $this->name . '::insert {requestId}', compact('requestId', 'entity'));
        }

        if ($this->debug === true) {
            $e = $this->stopwatch->start($this->name . '::insert#' . $requestId, 'suzie');
        }

        $return = $this->dataMapper->insert($entity);

        if (isset($e) && $e->isStarted()) {
            $e->stop();
        }

        return $return ?? false;
    }

    protected function belongsTo(string $relatedService, string $relatedId = null, string $storeInId = null)
    {
        if ($relatedId === null) {
            foreach ($relatedService::getInstance()->tableColumns as $columns) {
                if ($columns['Key'] === "PRI") {
                    $relatedId = $columns['Field'];
                }
            }
        }

        if ($storeInId === null) {
            $service = explode("\\", $relatedService);
            $storeInId = end($service);
        }

        $this->belongsTo[$storeInId] = [
            'service' => $relatedService,
            'relatedId' => $relatedId,
            'storeInId' => $storeInId
        ];
    }

    protected function hasOne(string $relatedService, string $relatedId = null, string $storeInId = null)
    {
        if ($relatedId === null) {
            if (!isset($this->tableColumns)) {
                $this->create();
            }
            foreach ($this->tableColumns as $columns) {
                if ($columns['Key'] === "PRI") {
                    $relatedId = $columns['Field'];
                }
            }
        }

        if ($storeInId === null) {
            $service = explode("\\", $relatedService);
            $storeInId = end($service);
        }

        $this->hasOne[$storeInId] = [
            'service' => $relatedService,
            'relatedId' => $relatedId,
            'storeInId' => $storeInId
        ];
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
                    } elseif ($column['Key'] === 'PRI' && $column['Field'] === 'id' && $column['Extra'] === 'auto_increment') {
                        $tableColumns[$column['Field']] = $column['Extra'];
                        $tableColumns['protectedId'] = $column['Field'];
                    } else {
                        $tableColumns[$column['Field']] = $column['Default'];
                    }
                }
            }
        }

        return $tableColumns ?? [];
    }
}