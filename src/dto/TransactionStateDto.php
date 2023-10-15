<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\dto;

use kuaukutsu\ds\dto\DtoBase;
use kuaukutsu\poc\saga\TransactionDataInterface;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-immutable
 */
final class TransactionStateDto extends DtoBase
{
    public string $step;

    public TransactionDataInterface $data;
}
