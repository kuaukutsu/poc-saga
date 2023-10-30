<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

final class Storage
{
    private static array $storage = [];

    public static function set(string $key, string $value): void
    {
        self::$storage[$key] = $value;
    }

    public static function get(string $key): ?string
    {
        return self::$storage[$key] ?? null;
    }

    public static function delete(string $key): void
    {
        unset(self::$storage[$key]);
    }

    public static function clean(): void
    {
        self::$storage = [];
    }

    public static function count(): int
    {
        return count(self::$storage);
    }
}
