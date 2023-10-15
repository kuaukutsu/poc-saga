<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\dto;

use kuaukutsu\ds\collection\Collection;
use kuaukutsu\poc\saga\TransactionDataInterface;

/**
 * @extends Collection<TransactionStateDto>
 */
final class TransactionStateCollection extends Collection
{
    public function getType(): string
    {
        return TransactionStateDto::class;
    }

    public function getData(string $stepName): ?TransactionDataInterface
    {
        return $this->get($stepName)?->data;
    }

    public function toArrayRecursive(): array
    {
        $collection = [];
        foreach ($this as $item) {
            $collection[] = $item->toArrayRecursive();
        }

        return $collection;
    }

    /**
     * @param TransactionStateDto $item
     */
    protected function indexBy($item): string
    {
        return $item->step;
    }
}
