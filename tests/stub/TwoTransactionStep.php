<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\TransactionStepBase;

final class TwoTransactionStep extends TransactionStepBase
{
    public function __construct(
        public readonly string $name,
    ) {
    }

    public function commit(): bool
    {
        Storage::set($this->name, 'test');

        $this->save(
            new TestTransactionData(
                name: $this->name,
                datetime: gmdate('c')
            )
        );

        return true;
    }

    public function rollback(): bool
    {
        Storage::delete($this->getModel()->name);

        return true;
    }

    private function getModel(): TestTransactionData
    {
        /**
         * TestTransactionData
         */
        return $this->get(TestTransactionData::class);
    }
}
