<?php

namespace KooijmanInc\Suzie\Wrapper;

use KooijmanInc\Suzie\Service\ServiceInterface;

trait SuzieWrapperTrait
{
    public function __construct(ServiceInterface $service)
    {
        parent::__construct($service);
        dump('wrap');
    }
}