<?php

namespace KooijmanInc\Suzie\Service;

use KooijmanInc\Suzie\Wrapper\SuzieWrapperTrait;

abstract class AbstractService implements ServiceInterface
{
    use SuzieWrapperTrait;

    public function __construct()
    {
        dump('that');
    }
}