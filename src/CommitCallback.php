<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Closure;
use kuaukutsu\poc\saga\state\TransactionStepStateCollection;

final class CommitCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(TransactionStepStateCollection, string): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(TransactionStepStateCollection $stack, string $uuid): void
    {
        call_user_func($this->callback, $stack, $uuid);
    }
}
