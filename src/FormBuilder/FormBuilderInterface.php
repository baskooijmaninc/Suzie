<?php

namespace KooijmanInc\Suzie\FormBuilder;

/**
 * Interface FormBuilderInterface
 *
 */
interface FormBuilderInterface extends \JsonSerializable, \ArrayAccess
{
    public function setColumns(string $columns);
}