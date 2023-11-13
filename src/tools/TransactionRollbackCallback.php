<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tools;

use Closure;
use Throwable;
use kuaukutsu\poc\saga\state\StepStateCollection;
use kuaukutsu\poc\saga\TransactionCallbackInterface;

final class TransactionRollbackCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(string, StepStateCollection, Throwable): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(string $uuid, StepStateCollection $stack, Throwable $exception): void
    {
        call_user_func($this->callback, $uuid, $stack, $exception);
    }
}
