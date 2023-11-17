<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use LogicException;

final class NotFoundException extends LogicException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
