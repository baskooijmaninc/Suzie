<?php

namespace KooijmanInc\Suzie;

use KooijmanInc\Suzie\FormBuilder\FormBuilderInterface;
use KooijmanInc\Suzie\Model\Entity\EntityInterface;
use Symfony\Component\Stopwatch\Stopwatch;

interface SuzieInterface
{
    /**
     * @param FormBuilderInterface $formBuilder
     * @return array|bool
     */
    public function hasRecord(FormBuilderInterface $formBuilder): array|bool;

    /**
     * @param array $data
     * @return AbstractSuzie
     */
    public function create(array $data = []): AbstractSuzie;

    /**
     * @param Stopwatch $stopwatch
     * @return SuzieInterface
     */
    public function setStopwatch(Stopwatch $stopwatch): SuzieInterface;

    /**
     * @param bool $debug
     * @return SuzieInterface
     */
    public function setDebug(bool $debug): SuzieInterface;
}