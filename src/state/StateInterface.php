<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\step\StepInterface;
use kuaukutsu\poc\saga\TransactionDataInterface;

interface StateInterface
{
    /**
     * @param class-string<StepInterface> $stepName
     */
    public function set(string $stepName, TransactionDataInterface $data): void;

    /**
     * @param class-string<StepInterface> $stepName
     * @throws NotFoundException Если значение не найдено.
     */
    public function get(string $stepName): TransactionDataInterface;

    /**
     * Каждый шаг может сохранять своё значение от выполнения действия.
     * В стеке содержится вся информация по выполненным шагам.
     */
    public function stack(): StepStateCollection;

    public function clean(): void;
}
