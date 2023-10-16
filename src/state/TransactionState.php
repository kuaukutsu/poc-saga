<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\exception\TransactionStateNotFoundException;
use kuaukutsu\poc\saga\step\TransactionStepInterface;

final class TransactionState implements TransactionStateInterface
{
    /**
     * При желании можно заменить на persistent storage.
     * @var array<class-string<TransactionStepInterface>, TransactionDataInterface>
     */
    private array $state = [];

    public function set(string $stepName, TransactionDataInterface $dto): void
    {
        $this->state[$stepName] = $dto;
    }

    public function get(string $stepName): TransactionDataInterface
    {
        return $this->state[$stepName]
            ?? throw new TransactionStateNotFoundException($stepName);
    }

    public function delete(string $stepName): void
    {
        unset($this->state[$stepName]);
    }

    public function stack(): TransactionStepStateCollection
    {
        return $this->stackToCollection($this->state);
    }

    /**
     * @param array<string, TransactionDataInterface> $stack
     */
    private function stackToCollection(array $stack): TransactionStepStateCollection
    {
        if ($stack === []) {
            return new TransactionStepStateCollection();
        }

        return new TransactionStepStateCollection(
            ...array_map(
                static fn(
                    string $step,
                    TransactionDataInterface $data
                ): TransactionStepState => new TransactionStepState($step, $data),
                array_keys($stack),
                $stack
            )
        );
    }
}
