<?php

namespace KooijmanInc\Suzie\Model\Entity;

use KooijmanInc\Suzie\SuzieInterface;

class EntityFactory
{
    public function create(SuzieInterface $suzie, string $entityClassName, iterable $data = [], bool $raw = false)
    {
        $entity = new $entityClassName($suzie);

        return $entity;
    }
}