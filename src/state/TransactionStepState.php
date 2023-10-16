<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

/**
 * @psalm-immutable
 */
final class TransactionStepState
{
    public function __construct(
        public readonly string $step,
        public readonly TransactionDataInterface $data,
    ) {
    }
}
