<?php

namespace KooijmanInc\Suzie\FormBuilder;

use KooijmanInc\Suzie\SuzieInterface;

class FormBuilderFactory
{
    public function create(SuzieInterface $suzie, $formBuilderClassName, iterable $data = [], bool $raw = false)
    {
        dump($data);
        $formBuilder = new $formBuilderClassName($suzie);

        return $formBuilder;
    }
}