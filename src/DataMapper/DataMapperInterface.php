<?php

namespace KooijmanInc\Suzie\DataMapper;

use KooijmanInc\Suzie\FormBuilder\FormBuilderFactory;
use KooijmanInc\Suzie\FormBuilder\FormBuilderInterface;
use KooijmanInc\Suzie\Model\Entity\EntityFactory;
use KooijmanInc\Suzie\Model\Entity\EntityInterface;
use KooijmanInc\Suzie\SuzieInterface;

/**
 * Interface DataMapperInterface
 * @package KooijmanInc\Suzie\DataMapper
 */
interface DataMapperInterface
{
    /**
     * @param FormBuilderFactory $formBuilderFactory
     * @param EntityFactory $entityFactory
     */
    public function __construct(FormBuilderFactory $formBuilderFactory, EntityFactory $entityFactory);

    public function setEntityClassName($entityClassName);

    public function setFormBuilderClassName($suzieEntityClassName);

    /**
     * @param SuzieInterface $suzie
     * @return DataMapperInterface
     */
    public function setSuzie(SuzieInterface $suzie): DataMapperInterface;

    /**
     * @param array $row
     * @param bool $checkSetup
     * @return FormBuilderInterface
     */
    public function rowToFormBuilder(array $row, array $base = [], bool $checkSetup = true): FormBuilderInterface;

    /**
     * @param array $row
     * @param bool $raw
     * @param bool $checkSetup
     * @return FormBuilderInterface
     */
    public function rowToEntity(array $row, bool $raw = true, bool $checkSetup = true): EntityInterface;
}