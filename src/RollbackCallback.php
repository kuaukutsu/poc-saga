<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Closure;
use Throwable;
use kuaukutsu\poc\saga\dto\TransactionStateCollection;

final class RollbackCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(TransactionStateCollection, Throwable, string): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(TransactionStateCollection $stack, Throwable $exception, string $uuid): void
    {
        call_user_func($this->callback, $stack, $exception, $uuid);
    }
}
