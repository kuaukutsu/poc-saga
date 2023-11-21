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
        $date = gmdate($this->dateFormat);

        Storage::set($date, 'test');

        $this->save(
            new TestTransactionData(
                name: $this->name,
                datetime: $date
            )
        );

        return true;
    }

    public function rollback(): bool
    {
        Storage::delete($this->getCurrentModel()->datetime);

        return true;
    }

    private function getCurrentModel(): TestTransactionData
    {
        /**
         * TestTransactionData
         */
        return $this->current();
    }
}
