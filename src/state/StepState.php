<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\step\StepInterface;
use kuaukutsu\poc\saga\TransactionDataInterface;

/**
 * @psalm-immutable
 */
final class StepState
{
    /**
     * @param class-string<StepInterface> $step
     */
    public function __construct(
        public readonly string $step,
        public readonly TransactionDataInterface $data,
    ) {
    }
}
