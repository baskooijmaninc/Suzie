<?php

namespace KooijmanInc\Suzie\Model\Entity;

/**
 * Interface EntityInterface
 * @package KooijmanInc\Suzie\Model\Entity
 */
interface EntityInterface extends \JsonSerializable, \ArrayAccess
{
    public function setRaw(string $property, $value): EntityInterface;

    public function save();
}