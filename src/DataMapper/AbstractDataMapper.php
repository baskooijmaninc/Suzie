<?php

namespace KooijmanInc\Suzie\DataMapper;

use KooijmanInc\Suzie\FormBuilder\FormBuilderFactory;
use KooijmanInc\Suzie\FormBuilder\FormBuilderInterface;
use KooijmanInc\Suzie\SuzieInterface;

/**
 * Class AbstractDataMapper
 * @package KooijmanInc\Suzie\DataMapper
 */
abstract class AbstractDataMapper implements DataMapperInterface
{
    protected $entityFactory;

    /**
     * @var ?FormBuilderFactory
     */
    protected ?FormBuilderFactory $formBuilderFactory;

    /**
     * @var SuzieInterface
     */
    protected SuzieInterface $suzie;

    protected string $entityClassName;

    protected string $formBuilderClassName;

    /**
     * @param ...$EntityOrForm
     */
    public function __construct($EntityOrForm)
    {
        if (isset($entityOrForm) && $entityOrForm instanceof FormBuilderFactory) {

        }
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

    public function rowToFormBuilder(array $row, bool $raw = true, bool $checkSetup = true): FormBuilderInterface
    {

        $form = $this->formBuilderFactory->create($this->suzie, $this->formBuilderClassName, $row, $raw);

        return $form;
    }
}