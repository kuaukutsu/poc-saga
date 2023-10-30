<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\step\TransactionStepBase;

final class SaveStep extends TransactionStepBase
{
    public function __construct(
        private readonly string $name,
    ) {
    }

    public function commit(): bool
    {
        Storage::set($this->name, 'test');

        return true;
    }

    public function rollback(): bool
    {
        Storage::delete($this->name);

        return true;
    }
}
