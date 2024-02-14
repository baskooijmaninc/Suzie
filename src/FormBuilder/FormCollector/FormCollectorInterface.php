<?php

namespace KooijmanInc\Suzie\FormBuilder\FormCollector;

use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;

/**
 * Interface FormElementsInterface
 * @package KooijmanInc\Suzie\FormBuilder\FormElements
 */
interface FormCollectorInterface
{
    /**
     * @return FormInterface
     */
    public function form(): FormInterface;

    public function getInputOptions(string $name, array $attributes);
}