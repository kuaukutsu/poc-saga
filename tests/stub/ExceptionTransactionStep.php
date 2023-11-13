<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\TransactionStepBase;
use RuntimeException;

final class ExceptionTransactionStep extends TransactionStepBase
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public function commit(): bool
    {
        $this->save(
            TestTransactionData::hydrate(
                [
                    'name' => $this->name,
                    'datetime' => gmdate('c'),
                ]
            )
        );

        throw new RuntimeException(
            'RuntimeException from FailureStep.'
        );
    }

    public function rollback(): bool
    {
        return true;
    }
}
