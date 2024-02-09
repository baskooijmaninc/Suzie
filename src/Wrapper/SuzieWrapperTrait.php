<?php

namespace KooijmanInc\Suzie\Wrapper;

trait SuzieWrapperTrait
{
    public function __construct()
    {
        parent::__construct();
        dump('wrap');
    }
}