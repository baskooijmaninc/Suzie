<?php

namespace KooijmanInc\Suzie\Model\Entity;

use KooijmanInc\Suzie\SuzieInterface;

/**
 * Class AbstractEntity
 * @property $id
 */
#[\AllowDynamicProperties]
abstract class AbstractEntity implements EntityInterface
{
    /**
     * @var EntityInterface|null
     */
    protected ?EntityInterface $previous = null;

    protected string $uuid;

    protected mixed $id;

    protected SuzieInterface $suzie;

    public function __construct(SuzieInterface $suzie)
    {
        $this->uuid = uniqid(str_replace('\\', '-', get_class($this)) . '-', true);
        $this->suzie = $suzie;
    }

    /**
     * @return EntityInterface
     */
    public function setPrevious(): EntityInterface
    {
        $this->previous = clone $this;

        return $this;
    }

    /**
     * @param string $property
     * @param $value
     * @return EntityInterface
     */
    public function setRaw(string $property, $value): EntityInterface
    {
        $this->{$property} = $value;

        return $this;
    }

    public function setId(mixed $id): EntityInterface
    {
        $this->id = $id;

        return $this;
    }

    public function &__get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }
    }

    public function __set(string $property, $value)
    {
        if (property_exists($this, $property)) {
            return $this->{$property} = $value;
        }
    }

    /**
     * @param bool $validate
     * @return bool
     */
    public function save(bool $validate = true): bool
    {
        return $this->suzie->save($this, $validate);
    }

    /**
     * @return bool|array
     */
    public function hasUnsavedChanges(): bool|array
    {
        $return = [];
        if (array_diff(get_object_vars(...)->__invoke($this), get_object_vars(...)->__invoke($this->previous))) {
            $entityColumns = get_object_vars(...)->__invoke($this);
            if (isset($entityColumns['protectedId'])) {
                $return['id'] = $this->id;
            }
            $return += $entityColumns;
        } else {
            $return = false;
        }

        return $return;
    }

    public function toArray(bool $hideNotInvoked = false): array
    {
        $data = [];
        $properties = get_object_vars(...)->__invoke($this);

        if (isset($properties['protectedId'])) {
            if ($this->id !== 'auto_increment') {
                $data['id'] = $this->id;
            } else {
                $data['id'] = null;
            }
            unset($properties['protectedId']);
        }

        $data += $properties;

        if ($hideNotInvoked) {
            foreach ($data as $key => $value) {
                if ($value === null) {
                    unset($data[$key]);
                }
            }
        }

        return $data;
    }

    public function toArrayForSave(): array
    {
        return $this->toArray();
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

    public function __call(string $name, array $arguments): mixed
    {
        dump("__call: ", $name, $arguments);
    }
}