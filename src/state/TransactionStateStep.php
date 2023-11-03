<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\step\TransactionStepInterface;

/**
 * @psalm-immutable
 */
final class TransactionStateStep
{
    /**
     * @param class-string<TransactionStepInterface> $step
     */
    public function __construct(
        public readonly string $step,
        public readonly TransactionDataInterface $data,
    ) {
    }
}
