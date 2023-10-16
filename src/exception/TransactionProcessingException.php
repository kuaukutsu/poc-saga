<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use Throwable;
use RuntimeException;
use kuaukutsu\poc\saga\step\TransactionStep;

final class TransactionProcessingException extends RuntimeException
{
    public function __construct(
        string $uuid,
        TransactionStep $stepConfiguration,
        ?Throwable $previous = null,
    ) {
        $message = "[$uuid] $stepConfiguration->class step failed.";
        if ($previous !== null) {
            $message .= ' Message: ' . $previous->getMessage();
        }

        parent::__construct($message, 0, $previous);
    }
}
