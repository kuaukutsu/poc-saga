<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\exception;

use LogicException;

final class NotFoundException extends LogicException
{
    public function __construct(string $stepName, int $code = 0)
    {
        parent::__construct("[$stepName] state not found.", $code);
    }
}
