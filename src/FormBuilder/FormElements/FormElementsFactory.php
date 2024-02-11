<?php

namespace KooijmanInc\Suzie\FormBuilder\FormElements;

use KooijmanInc\Suzie\FormBuilder\FormBuilderFactory;

class FormElementsFactory extends AbstractFormElements
{
    public function __construct(string $uuid)
    {
        parent::__construct($uuid);
    }
}