<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use function DI\autowire;

final class StepFactory
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function create(Step $stepConfiguration): StepInterface
    {
        $definition = autowire($stepConfiguration->class);
        foreach ($stepConfiguration->params as $key => $value) {
            $definition->constructorParameter($key, $value);
        }

        $container = new Container(
            [
                StepInterface::class => $definition,
            ]
        );

        /**
         * @var StepInterface
         */
        return $container->get(StepInterface::class);
    }
}
