<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\TransactionDataInterface;

final class TestTransactionData implements TransactionDataInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $datetime,
    ) {
    }
}
