<?php

namespace KooijmanInc\Suzie\FormBuilder\FormCollector;

/**
 * Interface FormElementsInterface
 * @package KooijmanInc\Suzie\FormBuilder\FormElements
 */
interface FormCollectorInterface
{
    public function form();

    public function getInputOptions(string $name, array $attributes);
}