<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\TransactionStepBase;

final class FailureStep extends TransactionStepBase
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public function commit(): bool
    {
        throw new \RuntimeException(
            'RuntimeException from FailureStep.'
        );
    }

    public function rollback(): bool
    {
        return true;
    }
}
