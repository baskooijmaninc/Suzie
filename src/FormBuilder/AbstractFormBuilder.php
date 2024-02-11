<?php

namespace KooijmanInc\Suzie\FormBuilder;

use KooijmanInc\Suzie\SuzieInterface;

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
    protected $suzie;

    /**
     * @var array
     */
    protected array $toBeSetInputs = [];

    /**
     * @var FormElements\FormElementsInterface
     */
    protected $formElements;

    public function __construct(SuzieInterface $suzie)
    {
        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);
        $this->suzie = $suzie;
        $this->formElements = new FormElements\FormElementsFactory($this->uuid);
    }

    public function setColumns(array $columns)
    {
        $this->{$columns['Field']} = $this->setValue($columns);


        return $this;
    }

    public function __set(string $name, $value)
    {
        if (property_exists($this, $name)) {
            dump('Found: ' . $name);
        } elseif (in_array($name, $this->toBeSetInputs, true)) {
            return $this->{$name} = $value;
        } else {
            dump("__set Still to do: ", $name, $value, $this->toBeSetInputs);
        }

    }

    public function &__get(string $name)
    {
        dump("__get: ", $name);
    }

    public function __call($name, $arguments)
    {
        dump("__call: ", $name, $arguments);
    }

    public function jsonSerialize(): mixed
    {
        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        return true;
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
        // TODO: Implement offsetUnset() method.
    }

    public function toBeSetInputs(array $inputs): void
    {
        foreach ($inputs as $key => $value) {
            if (!isset($this->toBeSetInputs[$key])) {
                $this->toBeSetInputs[] = $key;
            }
        }
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
}