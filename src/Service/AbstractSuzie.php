<?php

namespace KooijmanInc\Suzie\Service;


abstract class AbstractSuzie implements SuzieInterface
{

    public function __construct()
    {
        dump('that');
    }
}