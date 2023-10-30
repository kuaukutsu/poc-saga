<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use SplStack;
use Throwable;
use Exception;
use Ramsey\Uuid\UuidFactory;
use kuaukutsu\poc\saga\exception\TransactionProcessingException;
use kuaukutsu\poc\saga\state\TransactionState;
use kuaukutsu\poc\saga\state\TransactionStepState;
use kuaukutsu\poc\saga\state\TransactionStepStateCollection;
use kuaukutsu\poc\saga\step\TransactionStepFactory;
use kuaukutsu\poc\saga\step\TransactionStepInterface;

final class TransactionRunner
{
    private readonly string $uuid;

    /**
     * @var SplStack<TransactionStepInterface>
     */
    private readonly SplStack $stack;

    public function __construct(
        private readonly TransactionStepFactory $stepFactory,
        private readonly TransactionState $state,
        UuidFactory $uuidFactory,
    ) {
        $this->uuid = $uuidFactory->uuid7()->toString();
        $this->stack = new SplStack();
    }

    /**
     * @throws TransactionProcessingException Если транзакция потерпела фиаско.
     */
    public function run(
        TransactionInterface $transaction,
        TransactionCallbackInterface ...$listCallback,
    ): TransactionResult {
        [$commitCallback, $rollbackCallback] = $this->prepareCallback($listCallback);

        foreach ($transaction->steps() as $stepConfiguration) {
            try {
                $step = $this->stepFactory->create($stepConfiguration);
                $step->bind($this->uuid, $this->state);
                $isSuccess = $step->commit();
            } catch (Throwable $exception) {
                $this->rollback(
                    $exception,
                    $this->state->stack()->filter(
                        static fn(TransactionStepState $state): bool => $state->step !== $stepConfiguration->class
                    ),
                    $rollbackCallback,
                );

                throw new TransactionProcessingException($this->uuid, $stepConfiguration, $exception);
            }

            $this->stack->push($step);
            if ($isSuccess === false) {
                $exception = new TransactionProcessingException($this->uuid, $stepConfiguration);
                $this->rollback(
                    $exception,
                    $this->state->stack(),
                    $rollbackCallback,
                );

                throw $exception;
            }
        }

        return $this->commit($this->state->stack(), $commitCallback);
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

    private function rollback(
        Throwable $exception,
        TransactionStepStateCollection $stackState,
        ?RollbackCallback $callback,
    ): void {
        foreach ($this->stack as $step) {
            try {
                $step->rollback();
            } catch (Exception) {
                continue;
            }
        }

        $this->state->clean();
        $callback?->handler($stackState, $exception, $this->uuid);
    }

    private function commit(
        TransactionStepStateCollection $stackState,
        ?CommitCallback $callback,
    ): TransactionResult {
        $callback?->handler($stackState, $this->uuid);

        return new TransactionResult($this->uuid, $stackState);
    }
}
