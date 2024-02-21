<?php

namespace KooijmanInc\Suzie\FormValidation;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AbstractFormValidation
 * @package KooijmanInc\Suzie\FormValidation
 */
abstract class AbstractFormValidation implements FormValidationInterface
{
    public function &__get(string $name)
    {
        // TODO: Implement __get() method.
    }

    public function __set(string $name, $value)
    {
        // TODO: Implement __set() method.
    }
}