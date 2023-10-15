<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use Throwable;
use RuntimeException;
use kuaukutsu\poc\saga\dto\TransactionStepDto;

final class TransactionProcessingException extends RuntimeException
{
    public function __construct(
        string $uuid,
        TransactionStepDto $stepConfiguration,
        ?Throwable $previous = null,
    ) {
        $message = "[$uuid] $stepConfiguration->class step failed.";
        if ($previous !== null) {
            $message .= ' Message: ' . $previous->getMessage();
        }

        parent::__construct($message, 0, $previous);
    }
}
