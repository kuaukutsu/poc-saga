<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use kuaukutsu\poc\saga\exception\TransactionStateNotFoundException;
use kuaukutsu\poc\saga\state\TransactionDataInterface;
use kuaukutsu\poc\saga\state\TransactionStateInterface;

abstract class TransactionStepBase implements TransactionStepInterface
{
    private string $uuid;

    private TransactionStateInterface $state;

    final public function bind(string $uuid, TransactionStateInterface $state): void
    {
        $this->uuid = $uuid;
        $this->state = $state;
    }

    final protected function uuid(): string
    {
        return $this->uuid;
    }

    final protected function save(TransactionDataInterface $data): void
    {
        $this->state->set(static::class, $data);
    }

    /**
     * @param class-string<TransactionStepInterface> $stepName
     * @throws TransactionStateNotFoundException
     */
    final protected function get(string $stepName): TransactionDataInterface
    {
        return $this->state->get($stepName);
    }
}
