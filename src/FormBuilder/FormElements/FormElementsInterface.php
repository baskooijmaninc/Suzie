<?php

namespace KooijmanInc\Suzie\FormBuilder\FormElements;

/**
 * Interface FormElementsInterface
 * @package KooijmanInc\Suzie\FormBuilder\FormElements
 */
interface FormElementsInterface
{
    public function getInputOptions(string $name, array $attributes);
}