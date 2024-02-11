<?php

namespace KooijmanInc\Suzie\FormBuilder;

use KooijmanInc\Suzie\SuzieInterface;

class FormBuilderFactory
{
    public function create(SuzieInterface $suzie, $formBuilderClassName, iterable $data = [], bool $raw = false)
    {
        $formBuilder = new $formBuilderClassName($suzie);

        if (isset($data[0]['Field'])) {
            $this->fillBase($formBuilder, $data);
        }

        return $formBuilder;
    }

    private function fillBase(FormBuilderInterface &$formBuilder, array &$data)
    {
        foreach ($data as $columns) {
            $formBuilder->setColumns($columns);
        }

        return $formBuilder;
    }
}