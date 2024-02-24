<?php

namespace KooijmanInc\Suzie\FormValidation;

use KooijmanInc\Suzie\Exception\NotSupported;
use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Input\InputInterface;
use KooijmanInc\Suzie\Object\FormObject\ObjectInterface;

/**
 * Interface FormValidationInterface
 */
interface FormValidationInterface extends FormCollectorInterface
{
    /**
     * @return FormValidationInterface
     */
    public function setPrevious(): static;

    public function setIsValidated(FormInterface $form, InputInterface $formElements);

    public function setPregMatch($value);

    public function setValidation(array $dbColData = []): static;

    /**
     * @param string $name
     */
    public function &__get(string $name);

    /**
     * @param string $name
     * @param $value
     * @return mixed
     * @throws NotSupported
     */
    public function __set(string $name, $value);
}