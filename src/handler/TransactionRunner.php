<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\handler;

use Throwable;
use Exception;
use Ramsey\Uuid\UuidFactory;
use kuaukutsu\poc\saga\dto\TransactionStateCollection;
use kuaukutsu\poc\saga\dto\TransactionStateDto;
use kuaukutsu\poc\saga\dto\TransactionDto;
use kuaukutsu\poc\saga\exception\TransactionProcessingException;
use kuaukutsu\poc\saga\CommitCallback;
use kuaukutsu\poc\saga\RollbackCallback;
use kuaukutsu\poc\saga\TransactionCallbackInterface;
use kuaukutsu\poc\saga\TransactionInterface;
use kuaukutsu\poc\saga\TransactionStepInterface;

final class TransactionRunner
{
    public function __construct(
        private readonly TransactionStepFactory $stepFactory,
        private readonly TransactionState $state,
        private readonly UuidFactory $uuidFactory,
    ) {
    }

    /**
     * @throws TransactionProcessingException Если транзакция потерпела фиаско.
     */
    public function run(
        TransactionInterface $transaction,
        TransactionCallbackInterface ...$listCallback,
    ): TransactionDto {
        $uuid = $this->uuidFactory->uuid7()->toString();

        [$commitCallback, $rollbackCallback] = $this->prepareCallback($listCallback);

        $stack = [];
        foreach ($transaction->steps() as $stepConfiguration) {
            try {
                $step = $this->stepFactory->create($stepConfiguration);
                $step->bind($uuid, $this->state);
                $isSuccess = $step->commit();
            } catch (Throwable $exception) {
                $state = $this->state->stack()->filter(
                    static fn(TransactionStateDto $state): bool => $state->step !== $stepConfiguration->class
                );
                $this->rollbackStack($stack);
                $this->callbackRollback(
                    $uuid,
                    $exception,
                    $state,
                    $transaction->getRollbackCallback(),
                    $rollbackCallback,
                );

                throw new TransactionProcessingException($uuid, $stepConfiguration, $exception);
            }

            $stack[] = $step;
            if ($isSuccess === false) {
                $exception = new TransactionProcessingException($uuid, $stepConfiguration);
                $state = $this->state->stack();
                $this->rollbackStack($stack);
                $this->callbackRollback(
                    $uuid,
                    $exception,
                    $state,
                    $transaction->getRollbackCallback(),
                    $rollbackCallback,
                );

                throw $exception;
            }
        }

        $this->callbackCommit(
            $uuid,
            $this->state->stack(),
            $transaction->getCommitCallback(),
            $commitCallback,
        );

        return TransactionDto::hydrate(
            [
                'uuid' => $uuid,
                'state' => $this->state->stack(),
            ]
        );
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

    /**
     * @param TransactionStepInterface[] $steps
     */
    private function rollbackStack(array $steps): void
    {
        foreach ($steps as $step) {
            try {
                $step->rollback();
            } catch (Exception) {
                continue;
            }

            $this->state->delete($step::class);
        }
    }

    private function callbackRollback(
        string $uuid,
        Throwable $exception,
        TransactionStateCollection $stackState,
        ?RollbackCallback $classCallback,
        ?RollbackCallback $funcCallback,
    ): void {
        $classCallback?->handler($stackState, $exception, $uuid);
        $funcCallback?->handler($stackState, $exception, $uuid);
    }

    private function callbackCommit(
        string $uuid,
        TransactionStateCollection $stackState,
        ?CommitCallback $classCallback,
        ?CommitCallback $funcCallback,
    ): void {
        $classCallback?->handler($stackState, $uuid);
        $funcCallback?->handler($stackState, $uuid);
    }
}
