<?php

namespace KooijmanInc\Service;

use KooijmanInc\Suzie\Wrapper\SuzieWrapperTrait;

abstract class AbstractSuzie implements SuzieInterface
{
    use SuzieWrapperTrait;

    public function __construct()
    {
        dump('that');
    }
}