<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\handler;

use kuaukutsu\poc\saga\dto\TransactionStateCollection;
use kuaukutsu\poc\saga\dto\TransactionStateDto;
use kuaukutsu\poc\saga\exception\TransactionStateNotFoundException;
use kuaukutsu\poc\saga\TransactionDataInterface;
use kuaukutsu\poc\saga\TransactionStateInterface;
use kuaukutsu\poc\saga\TransactionStepInterface;

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

    public function stack(): TransactionStateCollection
    {
        return $this->stackToCollection($this->state);
    }

    /**
     * @param array<string, TransactionDataInterface> $stack
     */
    private function stackToCollection(array $stack): TransactionStateCollection
    {
        if ($stack === []) {
            return new TransactionStateCollection();
        }

        return new TransactionStateCollection(
            ...array_map(
                static fn(
                    string $step,
                    TransactionDataInterface $data
                ): TransactionStateDto => TransactionStateDto::hydrate(
                    [
                        'step' => $step,
                        'data' => $data,
                    ]
                ),
                array_keys($stack),
                $stack
            )
        );
    }
}
