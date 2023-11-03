<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\ds\collection\Collection;

/**
 * @extends Collection<TransactionStateStep>
 */
final class TransactionStateStepCollection extends Collection
{
    public function getType(): string
    {
        return TransactionStateStep::class;
    }

    public function getData(string $stepName): ?TransactionDataInterface
    {
        return $this->get($stepName)?->data;
    }

    public function withoutStep(string $stepName): self
    {
        return $this->filter(
            static fn(TransactionStateStep $state): bool => $state->step !== $stepName
        );
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
     * @param TransactionStateStep $item
     */
    protected function indexBy($item): string
    {
        return $item->step;
    }
}
