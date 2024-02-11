<?php

namespace KooijmanInc\Suzie\DataMapper;

use KooijmanInc\Suzie\FormBuilder\FormBuilderFactory;
use KooijmanInc\Suzie\FormBuilder\FormBuilderInterface;
use KooijmanInc\Suzie\Model\Entity\EntityFactory;
use KooijmanInc\Suzie\Model\Entity\EntityInterface;
use KooijmanInc\Suzie\SuzieInterface;

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
     */
    public function __construct(FormBuilderFactory $formBuilderFactory, EntityFactory $entityFactory)
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

    public function rowToFormBuilder(iterable $row, bool $raw = true, bool $checkSetup = true): FormBuilderInterface
    {
dump('here');
        $form = $this->formBuilderFactory->create($this->suzie, $this->formBuilderClassName, $row, $raw);

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
}