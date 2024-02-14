<?php

namespace KooijmanInc\Suzie\FormBuilder;

/**
 * Interface FormBuilderInterface
 *
 */
interface FormBuilderInterface extends \JsonSerializable, \ArrayAccess
{
    public function form();

    public function getForm();

    public function setColumns(array $columns);

    public function __set(string $name, $value);

    public function &__get(string $name);

    public function __isset(string $name): bool;
}