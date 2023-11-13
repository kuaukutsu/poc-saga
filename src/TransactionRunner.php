<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Throwable;
use Ramsey\Uuid\UuidFactory;
use kuaukutsu\poc\saga\exception\ProcessingException;
use kuaukutsu\poc\saga\exception\StepFactoryException;
use kuaukutsu\poc\saga\state\State;
use kuaukutsu\poc\saga\step\StepStack;
use kuaukutsu\poc\saga\step\StepFactory;
use kuaukutsu\poc\saga\step\StepInterface;
use kuaukutsu\poc\saga\tools\TransactionCommitCallback;
use kuaukutsu\poc\saga\tools\TransactionRollbackCallback;

final class TransactionRunner
{
    /**
     * @var non-empty-string
     */
    private readonly string $uuid;

    public function __construct(
        private readonly State $state,
        private readonly StepStack $stack,
        private readonly StepFactory $stepFactory,
        UuidFactory $uuidFactory,
    ) {
        $this->uuid = $uuidFactory->uuid7()->toString();
    }

    /**
     * @throws StepFactoryException Если транзакция потерпела фиаско.
     * @throws ProcessingException Если транзакция потерпела фиаско.
     */
    public function run(
        TransactionInterface $transaction,
        TransactionCallbackInterface ...$listCallback,
    ): TransactionResult {
        [$commitCallback, $rollbackCallback] = $this->prepareCallback($listCallback);

        $stepQueue = $this->stepFactory->createQueue($this->uuid, $transaction);
        foreach ($stepQueue as $step) {
            try {
                $step->bind($this->uuid, $this->state);
                $isSuccess = $step->commit();
            } catch (Throwable $exception) {
                $this->rollbackByException($exception, $step, $rollbackCallback);
                throw new ProcessingException($this->uuid, $step::class, $exception);
            }

            if ($isSuccess === false) {
                $this->rollbackByResult($step, $rollbackCallback);
                throw new ProcessingException($this->uuid, $step::class);
            }

            $this->stack->push($step);
        }

        return $this->commit($commitCallback);
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
        StepInterface $step,
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
        StepInterface $step,
        ?TransactionRollbackCallback $callback,
    ): void {
        $step->rollback();
        $this->stack->rollback();

        if ($callback !== null) {
            try {
                $callback->handler(
                    $this->uuid,
                    $this->state->stack(),
                    new ProcessingException($this->uuid, $step::class),
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
