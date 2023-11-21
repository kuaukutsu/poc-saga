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
     * Получить последнее/актуальное значение сохраненной модели.
     *
     * @param class-string<TransactionDataInterface> $name
     * @throws NotFoundException Если значение не найдено.
     */
    public function get(string $name): TransactionDataInterface;

    /**
     * Получить значение сохраненной модели в заданном шаге.
     * Отоличие от get(...) в том, что вернётся именно то значение, которое было сохранено в указанном шаге.
     *
     * @param class-string<StepInterface> $stepName
     * @throws NotFoundException Если значение не найдено.
     */
    public function getStepData(string $stepName): TransactionDataInterface;

    /**
     * Каждый шаг может сохранять своё значение от выполнения действия.
     * В стеке содержится вся информация по выполненным шагам.
     */
    public function stack(): StateCollection;

    public function clean(): void;
}
