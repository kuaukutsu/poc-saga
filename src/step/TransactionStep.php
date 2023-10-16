<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use kuaukutsu\ds\dto\DtoInterface;

/**
 * @psalm-immutable
 */
final class TransactionStep
{
    /**
     * @param class-string<TransactionStepInterface> $class
     * @param array<string, string|int|string[]|int[]|DtoInterface> $params Конфигурация объекта
     */
    public function __construct(
        public readonly string $class,
        public readonly array $params,
    ) {
    }
}
