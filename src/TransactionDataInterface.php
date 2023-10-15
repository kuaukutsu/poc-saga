<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

/**
 * Для обобщения данных которые являются результатом работы Step.
 * Только простые структуры: DtoInterface, Collection
 */
interface TransactionDataInterface
{
    /**
     * Converts the object into an array.
     *
     * @return array<string, mixed> the array representation of the object
     */
    public function toArray(): array;
}
