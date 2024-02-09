<?php

namespace KooijmanInc\Suzie;

use KooijmanInc\Service\ServiceInterface;

abstract class AbstractSuzie implements SuzieInterface
{
    protected ServiceInterface $service;

    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;
        dump($this);
    }

}