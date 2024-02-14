<?php

namespace KooijmanInc\Suzie\FormBuilder;

use KooijmanInc\Suzie\Exception\NotSupported;
use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollector;
use KooijmanInc\Suzie\FormBuilder\FormCollector\FormCollectorInterface;
use KooijmanInc\Suzie\FormBuilder\FormParts\Form\FormInterface;
use KooijmanInc\Suzie\SuzieInterface;
use ReturnTypeWillChange;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Class AbstractFormBuilder
 * @property $id
 */
abstract class AbstractFormBuilder implements FormBuilderInterface
{
    /**
     * @var string
     */
    protected string $uuid;

    /**
     * @var mixed
     */
    protected mixed $id;

    /**
     * @var SuzieInterface
     */
    protected SuzieInterface $suzie;

    /**
     * @var array
     */
    protected array $toBeSetInputs = [];

    /**
     * @var FormCollectorInterface
     */
    protected FormCollectorInterface $formCollector;

    /**
     * @var FormInterface
     */
    protected FormInterface $form;

    /**
     * @var string
     */
    protected string $completeForm = "";

    public function __construct(SuzieInterface $suzie)
    {
        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);
        $this->suzie = $suzie;
        $this->formCollector = new FormCollector($this->uuid);
    }

    public function getFormArray()
    {
        return [
            $this->formStart()
        ];
    }

    public function getCompleteForm()
    {
        return $this->completeForm = "hi";
    }

    public function setColumns(array $columns)
    {
        $this->{$columns['Field']} = $this->setValue($columns);

        return $this;
    }

    public function __set(string $name, $value)
    {
        $accessor = "set".ucfirst($name);

        if (property_exists($this, $name)) {
            return $this->{$name}();
        } elseif (array_key_exists($name, $this->toBeSetInputs)) {
            return $this->{$name} = $value;
        } else {
            dump("__set Still to do: ", $name, $value, $this->toBeSetInputs);
        }

    }

    public function &__get(string $name)
    {
        $accessor = "get" . ucfirst($name);

        if ((method_exists($this, $accessor) && is_callable([$this, $accessor]))) {
            $value = $this->{$accessor}();
            return $value;
        } elseif (array_key_exists($name, $this->toBeSetInputs)) {
            $value = $this->getInputOptions($name, $this->toBeSetInputs[$name]);
            return $value;
        } else {
            dump("__get: ", $name);
        }
        //dump($value);
        throw new NotSupported("__get ($name) is not supported!");

    }

    public function __isset(string $name): bool
    {
        if (in_array($name, $this->toBeSetInputs)) {
            return true;
        }
dump($name);
        return (bool)property_exists($this, $name);
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): null
    {
        return $this->toFriendlyArray();
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        dump('offsetUnset: ', $offset);
    }

    /**
     * @param $name
     * @param $arguments
     * @return FormCollector
     * @throws NotSupported
     */
    public function __call($name, $arguments)
    {
        $accessor = "get" . ucfirst($name);

        if (empty($arguments) && ($this->__isset($name) || $this->__isset($accessor))) {
            dump("__call: ", $name, $arguments);
            return $this->__get($name);
        }

        throw new NotSupported("__call ($name) with arguments: (".implode($arguments).") is not supported!");
    }

    public function getInputOptions(string $name, array $attributes): FormCollectorInterface
    {
        return $this->formCollector->getInputOptions($name, $attributes);
    }

    public function toBeSetInputs(array $inputs): void
    {
        foreach ($inputs as $key => $value) {
            if (!isset($this->toBeSetInputs[$key])) {
                $this->toBeSetInputs[$key] = $value;
            }
        }
    }

    public function toFriendlyArray()
    {
        return $this->toArray(true);
    }

    public function toArray(bool $hideObjects = false)
    {
        $data = get_object_vars($this);

        dump($data);

        return $data;
    }

    protected function setValue(mixed $value)
    {
        if (is_array($value) && array_key_exists('Field', $value)) {
            if ($value['Default'] === 'UNIXTIMESTAMP') {
                $value = time();
            } else {
                $value = $value['Default'];
            }
        }

        return $value ?? null;
    }

    protected function getForm()
    {dump('activated');
        return $this->formCollector->form();
    }

    private function formStart(): string
    {
        dump($this->formCollector->form());
        return "<form>";
    }
}