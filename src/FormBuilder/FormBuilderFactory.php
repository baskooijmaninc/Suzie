<?php

namespace KooijmanInc\Suzie\FormBuilder;

use KooijmanInc\Suzie\SuzieInterface;

class FormBuilderFactory
{
    public function create(SuzieInterface $suzie, $formBuilderClassName, array $data = [], bool $raw = false)
    {
        $formBuilder = new $formBuilderClassName($suzie);

        return $formBuilder;
    }
}