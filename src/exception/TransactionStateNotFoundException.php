<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use RuntimeException;

final class TransactionStateNotFoundException extends RuntimeException
{
    public function __construct(string $stepName, int $code = 0)
    {
        parent::__construct("[$stepName] state not found.", $code);
    }
}
