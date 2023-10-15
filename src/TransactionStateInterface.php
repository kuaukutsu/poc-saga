<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\dto\TransactionStateCollection;
use kuaukutsu\poc\saga\exception\TransactionStateNotFoundException;

/**
 * На тот случай, если будет реализация с persistent storage
 */
interface TransactionStateInterface
{
    /**
     * @param class-string<TransactionStepInterface> $stepName
     */
    public function set(string $stepName, TransactionDataInterface $dto): void;

    /**
     * @param class-string<TransactionStepInterface> $stepName
     * @throws TransactionStateNotFoundException Если значение не найдено.
     */
    public function get(string $stepName): TransactionDataInterface;

    /**
     * Каждый шаг может сохранять своё значение от выполнения действия.
     * В стеке содержится вся информация по выполненным шагам.
     */
    public function stack(): TransactionStateCollection;
}
