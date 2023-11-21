<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use Throwable;
use RuntimeException;

final class ProcessingException extends RuntimeException
{
    public function __construct(
        string $uuid,
        string $className,
        ?Throwable $previous = null,
    ) {
        $message = "[$uuid] $className failed.";
        if ($previous !== null) {
            $message .= PHP_EOL . $previous->getMessage();
        }

        parent::__construct($message, 0, $previous);
    }
}
