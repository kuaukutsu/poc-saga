<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use kuaukutsu\ds\collection\Collection;

/**
 * @extends Collection<TransactionStep>
 */
final class TransactionStepCollection extends Collection
{
    public function getType(): string
    {
        return TransactionStep::class;
    }
}
