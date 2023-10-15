<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\dto;

use kuaukutsu\ds\collection\Collection;

/**
 * @extends Collection<TransactionStepDto>
 */
final class TransactionStepCollection extends Collection
{
    public function getType(): string
    {
        return TransactionStepDto::class;
    }
}
