<?php

namespace KooijmanInc\Suzie;


use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;

abstract class AbstractSuzie implements SuzieInterface
{

    public function __construct(DataAccessInterface $dataAccess)
    {
        dump($dataAccess);
    }
}