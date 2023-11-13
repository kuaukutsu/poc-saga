<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tools;

use Closure;
use kuaukutsu\poc\saga\TransactionCallbackInterface;
use kuaukutsu\poc\saga\TransactionResult;

final class TransactionCommitCallback implements TransactionCallbackInterface
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
