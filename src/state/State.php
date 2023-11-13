<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\step\StepInterface;
use kuaukutsu\poc\saga\TransactionDataInterface;

final class State implements StateInterface
{
    /**
     * При желании можно заменить на persistent storage.
     * @var array<class-string<StepInterface>, StepState>
     */
    private array $state = [];

    public function set(string $stepName, TransactionDataInterface $data): void
    {
        $this->state[$stepName] = new StepState($stepName, $data);
    }

    public function get(string $stepName): TransactionDataInterface
    {
        if (array_key_exists($stepName, $this->state)) {
            return $this->state[$stepName]->data;
        }

        throw new NotFoundException($stepName);
    }

    public function stack(): StepStateCollection
    {
        return new StepStateCollection(...$this->state);
    }

    public function clean(): void
    {
        $this->state = [];
    }
}
