<?php

namespace KooijmanInc\Suzie\FormValidation;

use KooijmanInc\Suzie\Exception\NotSupported;
use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Input\InputInterface;
use KooijmanInc\Suzie\Helper\Common;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * @var array
     */
    protected array $decryptedRequest;

    protected $formElement;

    protected TranslatorInterface $translator;

    protected ?string $pregMatch = null;

    protected ?string $pregMatchMessage = null;

    protected int $minWidth = 0;

    protected string $minWidthMessage = 'mustBeMinimum';

    protected ?int $maxWidth = null;

    protected string $maxWidthMessage = 'mustBeMaximum';

    protected ?string $hashValue = null;

    protected bool $allowNull = true;

    protected bool $hasError = false;

    protected bool $hasWarning = false;

    protected bool $hasSuccess = false;

    protected bool $useSuccess = false;

    protected string $errorMessage = '';

    protected ?string $filterVar = null;

    protected ?string $locale = null;

    protected string $comparisonOperator = "=";

    protected string $logicalOperator = "AND";

    private array $hashValueAllowed = ['md5', 'sha1', 'encrypt'];

    private array $filterVarAllowed = ['FILTER_VALIDATE_BOOLEAN' => 258, 'FILTER_VALIDATE_BOOL' => 258, 'FILTER_VALIDATE_DOMAIN' => 277, 'FILTER_VALIDATE_EMAIL' => 274, 'FILTER_VALIDATE_FLOAT' => 259, 'FILTER_VALIDATE_INT' => 257, 'FILTER_VALIDATE_IP' => 275, 'FILTER_VALIDATE_MAC' => 276, 'FILTER_VALIDATE_REGEXP' => 272, 'FILTER_VALIDATE_URL' => 273];

    private ?string $name = null;

    public function __construct(RequestStack $requestStack, string $id, TranslatorInterface $translator)
    {
        $this->id = $id;
        $this->request = $requestStack->getCurrentRequest();
        $this->translator = $translator;
        $this->locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    /**
     * @return FormValidationInterface
     */
    public function setPrevious(): static
    {
        $this->previous = clone $this;

        return $this;
    }

    public function getIsValidated(): bool
    {
        if (isset($this->decryptedRequest, $this->name)) {
            if (!$validate = $this->validate($this->decryptedRequest[$this->name])) {
                $this->hasError = true;
            } else {
                if ($validate[0] === 'error') {
                    $this->hasError = true;
                    $this->errorMessage = $validate[1];
                    $this->formElement->value('');
                    $this->isValidated = false;
                } elseif ($validate[0] === 'warning') {
                    $this->hasWarning = true;
                    $this->errorMessage = $validate[1];
                    $this->formElement->value('');
                    $this->isValidated = false;
                } elseif ($validate[0] === true) {
                    if ($this->useSuccess === true) {
                        $this->hasSuccess = true;
                    }
                    $this->formElement->value($validate[1]);
                    $this->isValidated = true;
                }
            }
        }

        return $this->isValidated;
    }

    public function setIsValidated(FormInterface $form, InputInterface $formElements)
    {
        if (strtolower($this->request->getMethod()) === $form->method) {
            if ($form->method === 'post') {
                $request = $this->decryptRequest($this->request->request->all());
            } elseif ($form->method === 'get') {
                $request = $this->decryptRequest($this->request->query->all());
            }
            $this->decryptedRequest = $request ?? [];
            if (array_key_exists($formElements->getName(), $this->decryptedRequest) && $formElements->formElement !== 'button') {
                $this->name = $formElements->getName();
                $this->formElement = $formElements;
                $this->getIsValidated();
            }
        }

        return $this;
    }

    public function setPregMatch($value): static
    {
        $this->pregMatch = $value ?? null;

        return $this;
    }

    public function setValidation(array $dbColData = []): static
    {
        if ($dbColData !== null && $dbColData !== []) {
            if (str_contains(strtolower($dbColData['Field']), 'email') && $this->pregMatch === null) {
                $this->pregMatch = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
                $this->pregMatchMessage = "onlyCharactersAllowed";
                $this->filterVar = 'FILTER_VALIDATE_EMAIL';
                $this->minWidth = 8;
            }
            if (str_contains(strtolower($dbColData['Field']), 'password') && $this->pregMatch === null) {
                $this->pregMatch = "/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,20}$/";
                $this->pregMatchMessage = "onlyCharactersAllowed";
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

    public function setFilterVar(?string $varName): static
    {
        if (array_key_exists($varName, $this->filterVarAllowed) || $varName === null) {
            $this->filterVar = $varName;
        }

        return $this;
    }

    public function setMaxWidth(int $width): static
    {
        $this->maxWidth = $width;

        return  $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws NotSupported
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
     * @param $name
     * @param $value
     * @return mixed
     * @throws NotSupported
     */
    public function __set($name, $value)
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

        return $return ?? [];
    }

    protected function validate(string $value)
    {
        if (strlen($value) < $this->minWidth) {
            return ['warning', str_replace('NUMBER', $this->minWidth, $this->translator->trans($this->minWidthMessage, [], 'suzie', $this->locale))];
        }
        if (strlen($value) > $this->maxWidth) {
            return ['warning', str_replace('NUMBER', $this->maxWidth, $this->translator->trans($this->maxWidthMessage, [], 'suzie', $this->locale))];
        }
        if (empty($value) && $this->allowNull === false) {
            return ['error'];
        }
        if ($this->filterVar !== null) {
            if ($this->filterVar === 'FILTER_VALIDATE_EMAIL') {
                if (!filter_var($value, $this->filterVarAllowed[$this->filterVar])) {
                    return ['warning', $this->translator->trans('noValidEmail', [], 'suzie', $this->locale)];
                }

                $check = explode('@', $value);
                if (!checkdnsrr(array_pop($check), 'MX')) {
                    return ['error', $this->translator->trans('nonExistingEmail', [], 'suzie', $this->locale)];
                }
            }
        }
        if ($this->pregMatch !== null) {
            if (!preg_match($this->pregMatch, $value)) {
                return ['warning', str_replace('PREGMATCH', $this->pregMatch, $this->translator->trans($this->pregMatchMessage, [], 'suzie', $this->locale))];
            }
        }
        if ($this->hashValue !== null) {
            if ($this->hashValue === 'encrypt') {
                $value = Common::encrypt($value);
            } elseif ($this->hashValue === 'sha1') {
                $value = SHA1($value);
            } elseif ($this->hashValue === 'md5') {
                $value = MD5($value);
            }
        }

        return [true, $value];
    }
}