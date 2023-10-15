<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\handler;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\poc\saga\dto\TransactionStepDto;
use kuaukutsu\poc\saga\TransactionStepInterface;

use function DI\autowire;

final class TransactionStepFactory
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function create(TransactionStepDto $stepConfiguration): TransactionStepInterface
    {
        $definition = autowire($stepConfiguration->class);
        foreach ($stepConfiguration->params as $key => $value) {
            $definition->constructorParameter($key, $value);
        }

        $container = new Container(
            [
                TransactionStepInterface::class => $definition,
            ]
        );

        /**
         * @var TransactionStepInterface
         */
        return $container->get(TransactionStepInterface::class);
    }
}
