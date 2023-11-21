<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

final class TestFailureData
{
    public function __construct(
        public readonly string $name,
        public readonly string $datetime,
    ) {
    }
}
