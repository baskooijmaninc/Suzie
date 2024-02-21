<?php

namespace KooijmanInc\Suzie\FormValidation;

use KooijmanInc\Suzie\Exception\NotSupported;
use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Input\InputInterface;
use KooijmanInc\Suzie\Helper\Common;
use KooijmanInc\Suzie\Object\FormObject\ObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AbstractFormValidation
 * @package KooijmanInc\Suzie\FormValidation
 */
abstract class AbstractFormValidation implements FormValidationInterface
{
    protected string $id;

    /**
     * @var FormValidationInterface|null
     */
    protected ?FormValidationInterface $previous = null;

    /**
     * @var bool
     */
    protected bool $isValidated = false;

    protected Request $request;

    public function __construct(RequestStack $requestStack, string $id)
    {
        $this->id = $id;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return FormValidationInterface
     */
    public function setPrevious(): static
    {
        $this->previous = clone $this;

        return $this;
    }

    public function setIsValidated(FormInterface $form, InputInterface $formElement)
    {
        if (strtolower($this->request->getMethod()) === $form->method) {
            if ($form->method === 'post') {
                $request = $this->decryptRequest($this->request->request->all());
            } elseif ($form->method === 'get') {
                $request = $this->decryptRequest($this->request->query->all());
            }
            if (array_key_exists($formElement->getName(), $request)) {
                dump($form, $formElement);
                $formElement->value = $request[$formElement->getName()];
            }
        }
        return $formElement;
    }

    public function &__get(string $name)
    {
        $accessor = "get" . ucfirst($name);

        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {
            $value = $this->$accessor();

            return $value;
        } elseif (property_exists($this, $name)) {
            return $this->$name;
        }

        throw new NotSupported("__get: property or method ".get_called_class()."::{$name} is not supported");
    }

    public function __set(string $name, $value)
    {
        // TODO: Implement __set() method.
    }

    protected function decryptRequest(array $request): array
    {
        foreach ($request as $key => $value) {
            $return[Common::decrypt($key)] = $value;
        }

        return $return;
    }
}