<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\exception\TransactionStateNotFoundException;
use kuaukutsu\poc\saga\step\TransactionStepInterface;

final class TransactionState implements TransactionStateInterface
{
    /**
     * При желании можно заменить на persistent storage.
     * @var array<class-string<TransactionStepInterface>, TransactionStepState>
     */
    private array $state = [];

    public function set(string $stepName, TransactionDataInterface $data): void
    {
        $this->state[$stepName] = new TransactionStepState($stepName, $data);
    }

    public function get(string $stepName): TransactionDataInterface
    {
        if (array_key_exists($stepName, $this->state)) {
            return $this->state[$stepName]->data;
        }

        throw new TransactionStateNotFoundException($stepName);
    }

    public function delete(string $stepName): void
    {
        unset($this->state[$stepName]);
    }

    public function stack(): TransactionStepStateCollection
    {
        return new TransactionStepStateCollection(...$this->state);
    }
}
