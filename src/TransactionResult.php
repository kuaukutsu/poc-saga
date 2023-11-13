<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\state\StepStateCollection;

/**
 * @psalm-immutable
 */
final class TransactionResult
{
    public function __construct(
        public readonly string $uuid,
        public readonly StepStateCollection $state,
    ) {
    }
}
