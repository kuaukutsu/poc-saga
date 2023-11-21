<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\ds\collection\Collection;
use kuaukutsu\poc\saga\TransactionDataInterface;

/**
 * @extends Collection<TransactionDataInterface>
 */
final class StateCollection extends Collection
{
    public function getType(): string
    {
        return TransactionDataInterface::class;
    }

    /**
     * @param TransactionDataInterface $item
     */
    protected function indexBy($item): string
    {
        return $item::class;
    }
}
