<?php

namespace KooijmanInc\Suzie;

use KooijmanInc\Suzie\Service\ServiceInterface;

abstract class AbstractSuzie implements SuzieInterface
{
    protected ServiceInterface $service;

    public function __construct()
    {
        //$this->service = $service;
        dump($this);
    }

}