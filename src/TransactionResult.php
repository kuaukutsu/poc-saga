<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\state\StateCollection;

/**
 * @psalm-immutable
 */
final class TransactionResult
{
    public function __construct(
        public readonly string $uuid,
        public readonly StateCollection $state,
    ) {
    }
}
