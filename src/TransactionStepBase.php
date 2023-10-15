<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\exception\TransactionStateNotFoundException;

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

    final protected function save(TransactionDataInterface $dto): void
    {
        $this->state->set(static::class, $dto);
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
