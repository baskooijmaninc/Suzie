<?php

namespace KooijmanInc\Suzie\FormBuilder\FormCollector;

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

    public function getInputOptions(string $name, array $attributes)
    {
        dump($name, $attributes);
        return $this;
    }
}