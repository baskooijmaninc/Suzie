<?php

namespace KooijmanInc\Suzie\FormBuilder;

use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;

/**
 * Interface FormBuilderInterface
 *
 */
interface FormBuilderInterface extends FormCollectorInterface, \JsonSerializable, \ArrayAccess
{
    public function getFormArray();

    public function getCompleteForm();

    public function setColumns(array $columns);

    public function __set(string $name, $value);

    public function &__get(string $name);

    public function __isset(string $name): bool;
}