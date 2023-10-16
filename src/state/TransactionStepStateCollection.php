<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\ds\collection\Collection;

/**
 * @extends Collection<TransactionStepState>
 */
final class TransactionStepStateCollection extends Collection
{
    public function getType(): string
    {
        return TransactionStepState::class;
    }

    public function getData(string $stepName): ?TransactionDataInterface
    {
        return $this->get($stepName)?->data;
    }

    public function toArrayRecursive(): array
    {
        $collection = [];
        foreach ($this as $item) {
            $collection[$item->step] = $item->data->toArray();
        }

        return $collection;
    }

    /**
     * @param TransactionStepState $item
     */
    protected function indexBy($item): string
    {
        return $item->step;
    }
}
