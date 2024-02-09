<?php

namespace KooijmanInc\Suzie\Model\Entity;

use KooijmanInc\Suzie\SuzieInterface;

/**
 * Class AbstractEntity
 * @property $id
 */
abstract class AbstractEntity implements EntityInterface
{
    protected string $uuid;

    protected mixed $id;

    protected SuzieInterface $suzie;

    public function __construct(SuzieInterface $suzie)
    {
        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);
        $this->suzie = $suzie;
    }

    protected function setId(mixed $id): EntityInterface
    {
        $this->id = $id;

        return $this;
    }

    public function setRaw(string $property, $value): EntityInterface
    {

        $this->{$property} = $value;

        return $this;
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