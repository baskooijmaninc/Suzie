<?php

namespace KooijmanInc\Suzie\DataMapper;

use KooijmanInc\Suzie\FormBuilder\FormBuilderInterface;
use KooijmanInc\Suzie\SuzieInterface;

/**
 * Interface DataMapperInterface
 * @package KooijmanInc\Suzie\DataMapper
 */
interface DataMapperInterface
{
    /**
     * @param ...$entityOrForm
     */
    public function __construct($entityOrForm);

    public function setEntityClassName($entityClassName);

    public function setFormBuilderClassName($suzieEntityClassName);

    /**
     * @param SuzieInterface $suzie
     * @return DataMapperInterface
     */
    public function setSuzie(SuzieInterface $suzie): DataMapperInterface;

    /**
     * @param array $row
     * @param bool $raw
     * @param bool $checkSetup
     * @return FormBuilderInterface
     */
    public function rowToFormBuilder(array $row, bool $raw = true, bool $checkSetup = true): FormBuilderInterface;
}