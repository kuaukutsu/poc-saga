<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Throwable;
use Closure;
use kuaukutsu\poc\saga\state\TransactionStateStepCollection;

final class TransactionRollbackCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(string, TransactionStateStepCollection, Throwable): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(string $uuid, TransactionStateStepCollection $stack, Throwable $exception): void
    {
        call_user_func($this->callback, $uuid, $stack, $exception);
    }
}
