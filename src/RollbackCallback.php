<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Throwable;
use Closure;
use kuaukutsu\poc\saga\state\TransactionStepStateCollection;

final class RollbackCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(TransactionStepStateCollection, Throwable, string): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(TransactionStepStateCollection $stack, Throwable $exception, string $uuid): void
    {
        call_user_func($this->callback, $stack, $exception, $uuid);
    }
}
