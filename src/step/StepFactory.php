<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use SplDoublyLinkedList;
use SplQueue;
use DI\Container;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\exception\StepFactoryException;
use kuaukutsu\poc\saga\TransactionInterface;

final class StepFactory
{
    public function __construct(private readonly Container $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(Step $stepConfiguration): StepInterface
    {
        /**
         * @var StepInterface
         */
        return $this->container->make(
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
