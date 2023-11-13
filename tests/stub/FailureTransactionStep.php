<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\TransactionStepBase;

final class FailureTransactionStep extends TransactionStepBase
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public function commit(): bool
    {
        Storage::set($this->name, 'test-failure');

        $this->save(
            TestTransactionData::hydrate(
                [
                    'name' => $this->name,
                    'datetime' => gmdate('c'),
                ]
            )
        );

        return false;
    }

    public function rollback(): bool
    {
        Storage::delete($this->name);

        return true;
    }
}
