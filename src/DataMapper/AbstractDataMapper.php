<?php

namespace KooijmanInc\Suzie\DataMapper;

use KooijmanInc\Suzie\FormBuilder\FormBuilderFactory;
use KooijmanInc\Suzie\FormBuilder\FormBuilderInterface;
use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;
use KooijmanInc\Suzie\Model\Entity\EntityFactory;
use KooijmanInc\Suzie\Model\Entity\EntityInterface;
use KooijmanInc\Suzie\SuzieInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AbstractDataMapper
 * @package KooijmanInc\Suzie\DataMapper
 */
abstract class AbstractDataMapper implements DataMapperInterface
{
    /**
     * @var array
     */
    protected static array $cache = [];

    /**
     * @var DataAccessInterface
     */
    protected DataAccessInterface $dataAccess;

    /**
     * @var EntityFactory
     */
    protected EntityFactory $entityFactory;

    /**
     * @var FormBuilderFactory
     */
    protected FormBuilderFactory $formBuilderFactory;

    /**
     * @var SuzieInterface
     */
    protected SuzieInterface $suzie;

    protected string $entityClassName;

    protected string $formBuilderClassName;

    /**
     * @param FormBuilderFactory $formBuilderFactory
     * @param EntityFactory $entityFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(FormBuilderFactory $formBuilderFactory, EntityFactory $entityFactory, protected TranslatorInterface $translator, protected RequestStack $requestStack)
    {
        $this->formBuilderFactory = $formBuilderFactory;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @param SuzieInterface $suzie
     * @return DataMapperInterface
     */
    public function setSuzie(SuzieInterface $suzie): DataMapperInterface
    {
        $this->suzie = $suzie;

        return $this;
    }

    public function setDataAccess(DataAccessInterface $dataAccess): DataMapperInterface
    {
        $this->dataAccess = $dataAccess;

        return $this;
    }

    public function rowToFormBuilder(iterable $row, array $base = [], bool $raw = true, bool $checkSetup = true): FormBuilderInterface
    {
        foreach ($row as $inputs) {
            $toBeSetInputs[$inputs['Field']] = $inputs;
        }

        $form = $this->formBuilderFactory->create($this->suzie, $this->formBuilderClassName, $this->translator, $this->requestStack, $toBeSetInputs ?? [], $base, $raw);

        return $form;
    }

    public function rowToEntity(iterable $row, bool $raw = true, bool $checkSetup = true): EntityInterface
    {
        $entity = $this->entityFactory->create($this->suzie, $this->entityClassName, $row, $raw);

        if ($row['id'] !== null) {
            self::$cache[$this->entityClassName][(int)$row['id']] = $entity;
        }

        return $entity;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function checkEntityType($entity): bool
    {
//        dump($entity, $this->entityClassName);
//        dump($entity instanceof $this->entityClassName);
        return $entity instanceof $this->entityClassName;
    }

    public function insert(EntityInterface &$entity): bool
    {
        $this->checkSetup();

        foreach ($entity->toArrayForSave() as $field => $value) {
            if ($value !== null) {
                $fields[$field] = $value;
            }
        }
        if ($id = $this->dataAccess->insert($fields)) {
            $entity->id = $id;

            if ($this->isCached($id) == false) {
                self::$cache[$this->entityClassName][$id] = $entity;
            }
            return true;
        } else {
            return false;
        }

        return false;
    }

    public function isCached(int $id): bool
    {
        if (!isset(self::$cache[$this->entityClassName])) {
            dump('no cache');
            //self::$cache[$this->entityClassName] = [];
        } elseif (isset(self::$cache[$this->entityClassName][$id])) {
            dump('have cache');
        }

        return false;
    }

    protected function checkSetup(): void
    {
        if ($this->dataAccess === null) {
            throw new \Exception("Class " . get_class($this) . "need dataAcces to be set");
        }
    }
}