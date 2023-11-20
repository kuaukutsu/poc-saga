<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\TransactionDataInterface;

trait StepStateModel
{
    /**
     * @var array<string, TransactionDataInterface>
     */
    private array $modelIndex = [];

    /**
     * @param class-string<TransactionDataInterface> $name
     */
    private function getStateModel(string $name): ?TransactionDataInterface
    {
        return $this->modelIndex[$name] ?? null;
    }

    private function setStateModel(TransactionDataInterface $data): void
    {
        $this->modelIndex[$data::class] = $data;
    }
}
