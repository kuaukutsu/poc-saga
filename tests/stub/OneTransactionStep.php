<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\TransactionStepBase;

final class OneTransactionStep extends TransactionStepBase
{
    public function __construct(
        public readonly string $name,
        private readonly string $dateFormat = 'c',
    ) {
    }

    public function commit(): bool
    {
        $this->save(
            TestTransactionData::hydrate(
                [
                    'name' => $this->name,
                    'datetime' => gmdate($this->dateFormat),
                ]
            )
        );

        return true;
    }

    public function rollback(): bool
    {
        return true;
    }
}
