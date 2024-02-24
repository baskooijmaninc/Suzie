<?php

namespace KooijmanInc\Suzie\FormValidation;

use KooijmanInc\Suzie\Exception\NotSupported;
use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Input\InputInterface;
use KooijmanInc\Suzie\Helper\Common;
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

    protected ?string $pregMatch = null;

    protected int $minWidth = 0;

    protected ?int $maxWidth = null;

    protected ?string $hashValue = null;

    protected bool $allowNull = true;

    protected bool $hasError = false;

    protected bool $hasWarning = false;

    protected bool $hasSuccess = false;

    private array $hasValueAllowed = ['md5', 'sha1', 'encrypt'];

    public function __construct(RequestStack $requestStack, string $id)
    {
        $this->id = $id;
        $this->request = $requestStack->getCurrentRequest();
        //dump($this->request);
    }

    /**
     * @return FormValidationInterface
     */
    public function setPrevious(): static
    {
        $this->previous = clone $this;

        return $this;
    }

    public function setIsValidated(FormInterface $form, InputInterface $formElements)
    {
        if (strtolower($this->request->getMethod()) === $form->method) {
            if ($form->method === 'post') {
                $request = $this->decryptRequest($this->request->request->all());
            } elseif ($form->method === 'get') {
                $request = $this->decryptRequest($this->request->query->all());
            }
            if (array_key_exists($formElements->getName(), $request) && $formElements->formElement !== 'button') {
                if (false === $validate = $this->validate($request[$formElements->getName()])) {
                    $this->hasError = true;
                } else {
                    if ($validate[0] === 'error') {
                        $this->hasError = true;
                    } elseif ($validate[0] === 'warning') {
                        $this->hasWarning = true;
                    } else {
                        $this->hasSuccess = true;
                        $formElements->value($validate[1]);
                    }
                }
            }
        }

        return $this;
    }

    public function setPregMatch($value)
    {
        $this->pregMatch = $value ?? null;

        return $this;
    }

    public function setValidation(array $dbColData = []): static
    {
        if ($dbColData !== null && $dbColData !== []) {
            if ($dbColData['Field'] === 'email' && $this->pregMatch === null) {
                $this->pregMatch = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
                $this->minWidth = 8;
            }
            if ($dbColData['Field'] === 'password' && $this->pregMatch === null) {
                $this->pregMatch = "/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,20}$/";
                $this->hashValue = 'sha1';
                $this->minWidth = 8;
            }
            $number = (int)filter_var($dbColData['Type'], FILTER_SANITIZE_NUMBER_INT);
            if ($number > 0) {
                $this->maxWidth = $number;
            }
            if ($dbColData['Null'] === "NO") {
                $this->allowNull = false;
            }
        }

        return $this;
    }

    /**
     * @param string $name
     */
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

    /**
     * @param string $name
     * @param $value
     * @return mixed
     * @throws NotSupported
     */
    public function __set(string $name, $value)
    {
        $accessor = 'set' . ucfirst($name);

        if (method_exists($this, $accessor) && is_callable([$this, $accessor])) {
            return $this->$accessor($value[0] ?? null);
        }

        throw new NotSupported("__set: property or method ".get_called_class()."::{$name} is not supported");
    }

    public function __call(string $name, $arguments)
    {
        if (!empty($arguments)) {
            return $this->__set($name, $arguments);
        }

        throw new NotSupported("__call (".get_called_class()."::$name) with args: (".implode($arguments).") is not supported.");
    }

    protected function decryptRequest(array $request): array
    {
        foreach ($request as $key => $value) {
            $return[Common::decrypt($key)] = $value;
        }

        return $return;
    }

    protected function validate(string $value)
    {
        if (strlen($value) < $this->minWidth) {
            return ['warning'];
        }
        if (strlen($value) > $this->maxWidth) {
            return ['warning'];
        }
        if (empty($value) && $this->allowNull === false) {
            return ['error'];
        }

        return [true, $value];
    }
}