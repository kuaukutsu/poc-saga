<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Closure;
use kuaukutsu\poc\saga\state\TransactionStepStateCollection;

final class CommitCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(string, TransactionStepStateCollection): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(string $uuid, TransactionStepStateCollection $stack): void
    {
        call_user_func($this->callback, $uuid, $stack);
    }
}
