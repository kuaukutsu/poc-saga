<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\dto;

use kuaukutsu\ds\dto\DtoBase;
use kuaukutsu\ds\dto\DtoInterface;

/**
 * @psalm-immutable
 * @psalm-suppress MissingConstructor
 */
final class TransactionStepDto extends DtoBase
{
    /**
     * @var class-string
     */
    public string $class;

    /**
     * Конфигурация объекта.
     *
     * @var array<string, string|int|string[]|int[]|DtoInterface>
     */
    public array $params = [];
}
