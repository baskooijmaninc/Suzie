<?php

namespace KooijmanInc\Suzie\DataMapper;

class DataMapper extends AbstractDataMapper
{
    /**
     * @param $entityClassName
     * @return DataMapper
     */
    public function setEntityClassName($entityClassName): static
    {
        $this->entityClassName = $entityClassName;

        return $this;
    }

    /**
     * @param $suzieEntityClassName
     * @return DataMapper
     */
    public function setFormBuilderClassName($suzieEntityClassName): static
    {
        $this->formBuilderClassName = $suzieEntityClassName;

        return $this;
    }
}