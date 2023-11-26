<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use SplDoublyLinkedList;
use SplQueue;
use DI\FactoryInterface;
use DI\DependencyException;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\exception\StepFactoryException;
use kuaukutsu\poc\saga\TransactionInterface;

final class StepFactory
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly FactoryInterface $factory,
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(Step $stepConfiguration): StepInterface
    {
        if (is_a($stepConfiguration->class, StepInterface::class, true) === false) {
            throw new DependencyException(
                "[$stepConfiguration->class] configuration class must implement StepInterface."
            );
        }

        if ($stepConfiguration->params === []) {
            /**
             * @var StepInterface
             */
            return $this->container->get($stepConfiguration->class);
        }

        /**
         * @var StepInterface
         */
        return $this->factory->make(
            $stepConfiguration->class,
            $stepConfiguration->params,
        );
    }

    /**
     * @return iterable<StepInterface>
     * @throws StepFactoryException
     * @throws NotFoundException
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

        if ($queue->isEmpty()) {
            throw new NotFoundException(
                "[$uuid] Transaction step configuration empty."
            );
        }

        return $queue;
    }
}
