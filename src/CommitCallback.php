<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Closure;

final class CommitCallback implements TransactionCallbackInterface
{
    /**
     * @param Closure(string, TransactionResult): void $callback
     */
    public function __construct(private readonly Closure $callback)
    {
    }

    public function handler(string $uuid, TransactionResult $result): void
    {
        call_user_func($this->callback, $uuid, $result);
    }
}
