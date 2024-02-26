<?php

namespace KooijmanInc\Suzie\FormValidation;

use KooijmanInc\Suzie\Exception\NotSupported;
use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Input\InputInterface;

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

    public function setPregMatch($value): static;

    public function setValidation(array $dbColData = []): static;

    public function setFilterVar(?string $varName): static;

    public function setMaxWidth(int $width): static;

    public function getName(): ?string;

    /**
     * @throws NotSupported
     * @param string $name
     */
    public function &__get(string $name);

    /**
     * @param $name
     * @param $value
     * @return mixed
     * @throws NotSupported
     */
    public function __set($name, $value);
}