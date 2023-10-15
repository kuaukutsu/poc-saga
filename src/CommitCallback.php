<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Closure;
use kuaukutsu\poc\saga\dto\TransactionStateCollection;

final class CommitCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(TransactionStateCollection, string): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(TransactionStateCollection $stack, string $uuid): void
    {
        call_user_func($this->callback, $stack, $uuid);
    }
}
