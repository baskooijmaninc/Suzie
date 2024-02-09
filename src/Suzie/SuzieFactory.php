<?php

namespace src\Suzie;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use src\Exception\General;

class SuzieFactory
{
    /**
     * @var array
     */
    public static $services = [];

    /**
     * @var ContainerInterface
     */
    protected static ContainerInterface $diContainer;

    public function __construct(ContainerInterface $diContainer)
    {
        self::$diContainer = $diContainer;
    }

    /**
     * @param ContainerInterface $diContainer
     */
    public static function setDiContainer(ContainerInterface $diContainer): void
    {
        self::$diContainer = $diContainer;
    }

    /**
     * @param $service
     * @return mixed
     * @throws General
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function create($service): mixed
    {
        if (self::$diContainer == null) {
            throw new General('SuzieFactory needs a dependency injection container to build the collectors');
        }

        if (!isset(self::$services[$service])) {
            self::$services[$service] = self::$diContainer->get($service);
        }

        return self::$services[$service];
    }
}