<?php

namespace KooijmanInc\Suzie\Model\Entity;

/**
 * Interface EntityInterface
 * @package KooijmanInc\Suzie\Model\Entity
 */
interface EntityInterface extends \JsonSerializable, \ArrayAccess
{
    /**
     * @return EntityInterface
     */
    public function setPrevious(): EntityInterface;

    public function setRaw(string $property, $value): EntityInterface;

    public function &__get(string $name);

    /**
     * @param bool $validate
     * @return bool
     */
    public function save(bool $validate = true): bool;

    public function saveAll(bool $validate = true): bool;

    /**
     * @return bool|array
     */
    public function hasUnsavedChanges(): bool|array;

    public function toArray(bool $hideNotInvoked = false): array;

    public function toArrayForSave(): array;

    public function toBeSetInputs(array $inputs): void;
}