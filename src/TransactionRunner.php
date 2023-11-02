<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use SplQueue;
use SplDoublyLinkedList;
use Throwable;
use Ramsey\Uuid\UuidFactory;
use Psr\Container\ContainerExceptionInterface;
use kuaukutsu\poc\saga\exception\TransactionFactoryException;
use kuaukutsu\poc\saga\exception\TransactionProcessingException;
use kuaukutsu\poc\saga\state\TransactionState;
use kuaukutsu\poc\saga\state\TransactionStepStateCollection;
use kuaukutsu\poc\saga\step\TransactionStepFactory;
use kuaukutsu\poc\saga\step\TransactionStepInterface;

final class TransactionRunner
{
    private readonly string $uuid;

    public function __construct(
        private readonly TransactionStepFactory $stepFactory,
        private readonly TransactionState $state,
        private readonly TransactionStack $stack,
        UuidFactory $uuidFactory,
    ) {
        $this->uuid = $uuidFactory->uuid7()->toString();
    }

    /**
     * @throws TransactionFactoryException Если транзакция потерпела фиаско.
     * @throws TransactionProcessingException Если транзакция потерпела фиаско.
     */
    public function run(
        TransactionInterface $transaction,
        TransactionCallbackInterface ...$listCallback,
    ): TransactionResult {
        [$commitCallback, $rollbackCallback] = $this->prepareCallback($listCallback);

        foreach ($this->factorySteps($transaction) as $step) {
            try {
                $step->bind($this->uuid, $this->state);
                $isSuccess = $step->commit();
            } catch (Throwable $exception) {
                throw $this->rollbackByException($exception, $step, $rollbackCallback);
            }

            if ($isSuccess === false) {
                throw $this->rollbackByResult($step, $rollbackCallback);
            }

            $this->stack->push($step);
        }

        return $this->commit($this->state->stack(), $commitCallback);
    }

    /**
     * @return iterable<TransactionStepInterface>
     * @throws TransactionFactoryException
     */
    private function factorySteps(TransactionInterface $transaction): iterable
    {
        /**
         * @var SplQueue<TransactionStepInterface> $queue
         */
        $queue = new SplQueue();
        $queue->setIteratorMode(SplDoublyLinkedList::IT_MODE_DELETE);
        foreach ($transaction->steps() as $stepConfiguration) {
            try {
                $queue->enqueue(
                    $this->stepFactory->create($stepConfiguration)
                );
            } catch (ContainerExceptionInterface $exception) {
                throw new TransactionFactoryException($this->uuid, $stepConfiguration->class, $exception);
            }
        }

        return $queue;
    }

    /**
     * @param TransactionCallbackInterface[] $listCallback
     * @return array{CommitCallback|null, RollbackCallback|null}
     */
    private function prepareCallback(array $listCallback): array
    {
        $commitCallback = null;
        $rollbackCallback = null;
        foreach ($listCallback as $callback) {
            if ($callback instanceof RollbackCallback) {
                $rollbackCallback = $callback;
            } elseif ($callback instanceof CommitCallback) {
                $commitCallback = $callback;
            }
        }

        return [$commitCallback, $rollbackCallback];
    }

    private function rollbackByException(
        Throwable $exception,
        TransactionStepInterface $step,
        ?RollbackCallback $callback,
    ): TransactionProcessingException {
        $this->stack->rollback();

        $stateCopy = $this->state->stack()->withoutStep($step::class);
        $this->state->clean();

        $callback?->handler($this->uuid, $stateCopy, $exception);

        return new TransactionProcessingException($this->uuid, $step::class, $exception);
    }

    private function rollbackByResult(
        TransactionStepInterface $step,
        ?RollbackCallback $callback,
    ): TransactionProcessingException {
        $step->rollback();
        $this->stack->rollback();

        $stateCopy = $this->state->stack();
        $this->state->clean();

        $exception = new TransactionProcessingException($this->uuid, $step::class);
        $callback?->handler($this->uuid, $stateCopy, $exception);

        return $exception;
    }

    private function commit(
        TransactionStepStateCollection $stackState,
        ?CommitCallback $callback,
    ): TransactionResult {
        $callback?->handler($this->uuid, $stackState);

        return new TransactionResult($this->uuid, $stackState);
    }
}
