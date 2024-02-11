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
    protected SuzieInterface $suzie;

    /**
     * @var FormElements\FormElementsInterface
     */
    protected $formElements;

    public function __construct(SuzieInterface $suzie)
    {
        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);
        $this->suzie = $suzie;
        $this->formElements = new FormElements\FormElementsFactory($this->uuid);
        dump($this->formElements);
    }

    public function setColumns(array $columns)
    {
        $this->{$columns['Field']} = "setElement";
    }

    public function __set(string $name, $value)
    {

        dump("__set: ", $name, $value);
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
}