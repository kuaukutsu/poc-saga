<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use SplDoublyLinkedList;
use SplQueue;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\poc\saga\exception\StepFactoryException;
use kuaukutsu\poc\saga\TransactionInterface;

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

    /**
     * @return iterable<StepInterface>
     * @throws StepFactoryException
     */
    public function createQueue(string $uuid, TransactionInterface $transaction): iterable
    {
        /**
         * @var SplQueue<StepInterface> $queue
         */
        $queue = new SplQueue();
        $queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
        foreach ($transaction->steps() as $stepConfiguration) {
            try {
                $queue->enqueue(
                    $this->create($stepConfiguration)
                );
            } catch (ContainerExceptionInterface $exception) {
                throw new StepFactoryException($uuid, $stepConfiguration->class, $exception);
            }
        }

        return $queue;
    }
}
