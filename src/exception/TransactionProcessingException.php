<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use Throwable;
use RuntimeException;

final class TransactionProcessingException extends RuntimeException
{
    public function __construct(
        string $uuid,
        string $stepClassName,
        ?Throwable $previous = null,
    ) {
        $message = "[$uuid] $stepClassName step failed.";
        if ($previous !== null) {
            $message .= ' ' . $previous->getMessage();
        }

        parent::__construct($message, 0, $previous);
    }
}
