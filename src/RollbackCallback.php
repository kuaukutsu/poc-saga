<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Throwable;
use Closure;
use kuaukutsu\poc\saga\state\TransactionStepStateCollection;

final class RollbackCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(string, TransactionStepStateCollection, Throwable): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(string $uuid, TransactionStepStateCollection $stack, Throwable $exception): void
    {
        call_user_func($this->callback, $uuid, $stack, $exception);
    }
}
