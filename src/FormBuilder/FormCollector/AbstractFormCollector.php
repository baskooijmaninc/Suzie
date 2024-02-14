<?php

namespace KooijmanInc\Suzie\FormBuilder\FormCollector;

use KooijmanInc\Suzie\FormBuilder\FormParts\Form\Form;

/**
 * Class AbstractFormElements
 * @package KooijmanInc\Suzie\FormBuilder\FormElements
 */
abstract class AbstractFormCollector implements FormCollectorInterface
{
    protected string $id;

    public function __construct(string $uuid)
    {
        $this->id = $uuid."-formCollector";
    }

    public function form()
    {
        $form = new Form($this->id);

        return $form;
    }

    public function getInputOptions(string $name, array $attributes)
    {
        dump($name, $attributes);
        return $this;
    }
}