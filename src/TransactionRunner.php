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
                $this->rollbackByException($exception, $step, $rollbackCallback);
                throw new TransactionProcessingException($this->uuid, $step::class, $exception);
            }

            if ($isSuccess === false) {
                $this->rollbackByResult($step, $rollbackCallback);
                throw new TransactionProcessingException($this->uuid, $step::class);
            }

            $this->stack->push($step);
        }

        return $this->commit($commitCallback);
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
     * @return array{TransactionCommitCallback|null, TransactionRollbackCallback|null}
     */
    private function prepareCallback(array $listCallback): array
    {
        $commitCallback = null;
        $rollbackCallback = null;
        foreach ($listCallback as $callback) {
            if ($callback instanceof TransactionRollbackCallback) {
                $rollbackCallback = $callback;
            } elseif ($callback instanceof TransactionCommitCallback) {
                $commitCallback = $callback;
            }
        }

        return [$commitCallback, $rollbackCallback];
    }

    private function rollbackByException(
        Throwable $exception,
        TransactionStepInterface $step,
        ?TransactionRollbackCallback $callback,
    ): void {
        $this->stack->rollback();

        if ($callback !== null) {
            try {
                $callback->handler(
                    $this->uuid,
                    $this->state->stack()->withoutStep($step::class),
                    $exception,
                );
            } catch (Throwable) {
            }
        }

        $this->state->clean();
    }

    private function rollbackByResult(
        TransactionStepInterface $step,
        ?TransactionRollbackCallback $callback,
    ): void {
        $step->rollback();
        $this->stack->rollback();

        if ($callback !== null) {
            try {
                $callback->handler(
                    $this->uuid,
                    $this->state->stack(),
                    new TransactionProcessingException($this->uuid, $step::class),
                );
            } catch (Throwable) {
            }
        }

        $this->state->clean();
    }

    private function commit(
        ?TransactionCommitCallback $callback,
    ): TransactionResult {
        $result = new TransactionResult($this->uuid, $this->state->stack());

        if ($callback !== null) {
            try {
                $callback->handler($this->uuid, $result);
            } catch (Throwable) {
            }
        }

        return $result;
    }
}
