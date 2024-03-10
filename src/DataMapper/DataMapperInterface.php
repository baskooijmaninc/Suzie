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
 * Interface DataMapperInterface
 * @package KooijmanInc\Suzie\DataMapper
 */
interface DataMapperInterface
{
    /**
     * @param FormBuilderFactory $formBuilderFactory
     * @param EntityFactory $entityFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(FormBuilderFactory $formBuilderFactory, EntityFactory $entityFactory, TranslatorInterface $translator, RequestStack $requestStack);

    public function setEntityClassName($entityClassName);

    public function setFormBuilderClassName($suzieEntityClassName);

    /**
     * @param SuzieInterface $suzie
     * @return DataMapperInterface
     */
    public function setSuzie(SuzieInterface $suzie): DataMapperInterface;

    /**
     * @param DataAccessInterface $dataAccess
     * @return DataMapperInterface
     */
    public function setDataAccess(DataAccessInterface $dataAccess): DataMapperInterface;

    /**
     * @param array $row
     * @param array $base
     * @param bool $checkSetup
     * @param bool $raw
     * @return FormBuilderInterface
     */
    public function rowToFormBuilder(array $row, array $base = [], bool $raw = true, bool $checkSetup = true): FormBuilderInterface;

    /**
     * @param array $row
     * @param bool $raw
     * @param bool $checkSetup
     * @return EntityInterface
     */
    public function rowToEntity(array $row, bool $raw = true, bool $checkSetup = true): EntityInterface;

    /**
     * @param $entity
     * @return bool
     */
    public function checkEntityType($entity): bool;

    public function insert(EntityInterface &$entity): bool;

    public function isCached(int $id): bool;
}