<?php

namespace KooijmanInc\Suzie;

use Symfony\Component\Stopwatch\Stopwatch;

interface SuzieInterface
{
    /**
     * @param array $data
     * @return AbstractSuzie
     */
    public function create(array $data = []): static;

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