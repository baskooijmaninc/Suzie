<?php

namespace KooijmanInc\Suzie\Model\Entity;

use KooijmanInc\Suzie\SuzieInterface;

class EntityFactory
{
    public function create(SuzieInterface $suzie, string $entityClassName, iterable $data = [], bool $raw = false)
    {
        $entity = new $entityClassName($suzie);

        if (!empty($data)) {
            if ($raw === true) {
                $entity = $this->fillRaw($entity, $data);
            } else {
                $entity = $this->fillNormal($entity, $data);
            }
            $entity->setPrevious();
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param array $data
     * @return EntityInterface
     */
    protected function fillNormal(EntityInterface &$entity, array &$data): EntityInterface
    {
        foreach ($data as $k => $v) {
            if ($k === 'id') {

            } else {
                $entity->{$k} = $v;
            }
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @param array $data
     * @return EntityInterface
     */
    protected function fillRaw(EntityInterface &$entity, array &$data): EntityInterface
    {
        foreach ($data as $k => $v) {
            $entity->setRaw($k, $v);
        }

        return $entity;
    }
}