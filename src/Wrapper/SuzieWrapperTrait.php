<?php

namespace KooijmanInc\Suzie\Wrapper;

use KooijmanInc\Service\ServiceInterface;

trait SuzieWrapperTrait
{
    public function __construct(ServiceInterface $service)
    {
        parent::__construct($service);
        dump('wrap');
    }
}