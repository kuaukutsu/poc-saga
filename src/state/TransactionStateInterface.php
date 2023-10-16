<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\exception\TransactionStateNotFoundException;
use kuaukutsu\poc\saga\step\TransactionStepInterface;

interface TransactionStateInterface
{
    /**
     * @param class-string<TransactionStepInterface> $stepName
     */
    public function set(string $stepName, TransactionDataInterface $data): void;

    /**
     * @param class-string<TransactionStepInterface> $stepName
     * @throws TransactionStateNotFoundException Если значение не найдено.
     */
    public function get(string $stepName): TransactionDataInterface;

    /**
     * @param class-string<TransactionStepInterface> $stepName
     */
    public function delete(string $stepName): void;

    /**
     * Каждый шаг может сохранять своё значение от выполнения действия.
     * В стеке содержится вся информация по выполненным шагам.
     */
    public function stack(): TransactionStepStateCollection;
}
