<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\dto;

use kuaukutsu\ds\dto\DtoBase;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-immutable
 */
final class TransactionDto extends DtoBase
{
    public string $uuid;

    public TransactionStateCollection $state;
}
