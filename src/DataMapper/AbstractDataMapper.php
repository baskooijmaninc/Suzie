<?php

namespace KooijmanInc\Suzie\DataMapper;

/**
 * Class AbstractDataMapper
 * @package KooijmanInc\Suzie\DataMapper
 */
abstract class AbstractDataMapper implements DataMapperInterface
{
    protected string $entityClassName;

    protected string $formBuilderClassName;

    public function __construct(...$EntityOrForm)
    {

    }
}