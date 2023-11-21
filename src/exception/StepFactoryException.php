<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use Throwable;
use RuntimeException;

final class StepFactoryException extends RuntimeException
{
    public function __construct(string $uuid, string $className, Throwable $previous)
    {
        $message = "[$uuid] $className step factory failed." .
            PHP_EOL . $previous->getMessage();

        parent::__construct($message, 0, $previous);
    }
}
