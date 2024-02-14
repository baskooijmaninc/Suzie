<?php

namespace KooijmanInc\Suzie\FormBuilder\FormCollector;

use KooijmanInc\Suzie\FormBuilder\FormParts\Form\Form;
use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;

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

    /**
     * @return FormInterface
     */
    public function form(): FormInterface
    {
        $form = new Form($this->id);

        return $form;
    }

    public function getInputOptions(string $name, array $attributes)
    {
        dump($name, $attributes);
        return $this;
    }

    public function __set(string $name, $value)
    {
        // TODO: Implement __set() method.
    }

    public function &__get(string $name)
    {
        // TODO: Implement __get() method.
    }
}