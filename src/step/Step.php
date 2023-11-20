<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

/**
 * @psalm-immutable
 */
final class Step
{
    /**
     * @param class-string<StepInterface> $class
     * @param array<string, string|int|string[]|int[]|object> $params Конфигурация объекта
     */
    public function __construct(
        public readonly string $class,
        public readonly array $params = [],
    ) {
    }
}
