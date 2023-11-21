<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\state\StateInterface;
use kuaukutsu\poc\saga\step\StepInterface;

abstract class TransactionStepBase implements StepInterface
{
    /**
     * @psalm-immutable
     */
    private string $uuid;

    /**
     * @psalm-immutable
     */
    private StateInterface $state;

    final public function bind(string $uuid, StateInterface $state): void
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
     * @param class-string<TransactionDataInterface> $modelName
     * @throws NotFoundException
     */
    final protected function get(string $modelName): TransactionDataInterface
    {
        return $this->state->get($modelName);
    }

    /**
     * @throws NotFoundException
     */
    final protected function current(): TransactionDataInterface
    {
        return $this->state->getStepData(static::class);
    }
}
