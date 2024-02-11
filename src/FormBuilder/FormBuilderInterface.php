<?php

namespace KooijmanInc\Suzie\FormBuilder;

/**
 * Interface FormBuilderInterface
 *
 */
interface FormBuilderInterface extends \JsonSerializable, \ArrayAccess
{
    public function setColumns(array $columns);

    public function __set(string $name, $value);

    public function &__get(string $name);
}