<?php

namespace KooijmanInc\Suzie\FormBuilder;

use KooijmanInc\Suzie\SuzieInterface;

class FormBuilderFactory
{
    protected array $protectedNames = ['toBeSetInputs', 'formElements'];

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
        foreach ($data as $inputs) {
            $toBeSetInputs[$inputs['Field']] = $inputs;
        }
        $formBuilder->toBeSetInputs($toBeSetInputs ?? []);
        foreach ($data as $columns) {
            if (isset($columns['Field']) && !in_array($columns['Field'], $this->protectedNames)) {
                $formBuilder->setColumns($columns);
            } elseif (!in_array($columns['Field'], $this->protectedNames)) {
                dump('factory still to do: ', $columns);
            }
        }

        return $formBuilder;
    }
}