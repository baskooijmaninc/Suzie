<?php

namespace KooijmanInc\Suzie\FormBuilder\FormElements;

/**
 * Class AbstractFormElements
 * @package KooijmanInc\Suzie\FormBuilder\FormElements
 */
abstract class AbstractFormElements implements FormElementsInterface
{
    protected string $id;

    public function __construct(string $uuid)
    {
        $this->id = $uuid."-formElements";
    }
}