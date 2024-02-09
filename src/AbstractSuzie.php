<?php

namespace KooijmanInc\Suzie;


use KooijmanInc\Suzie\Model\DataAccess\AbstractDataAccess;
use KooijmanInc\Suzie\Model\DataAccess\DataAccessInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractSuzie implements SuzieInterface
{
    /**
     * @var DataAccessInterface
     */
    protected DataAccessInterface $dataAccess;

    /**
     * @param AbstractDataAccess $dataAccess
     * @return AbstractSuzie
     */
    #[Required]
    public function init(AbstractDataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;

        return $this;
    }

    public function __construct()
    {
        dump();
    }
}