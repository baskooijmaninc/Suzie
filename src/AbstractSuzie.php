<?php

namespace KooijmanInc\Suzie;

use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;

abstract class AbstractSuzie implements SuzieInterface
{
    /**
     * @var DataAccessInterface
     */
    protected DataAccessInterface $dataAccess;

    public function __construct(DataAccessInterface $dataAccess)
    {
        $this->dataAccess = $dataAccess;
        dump($this);
    }
}