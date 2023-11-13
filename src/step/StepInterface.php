<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use kuaukutsu\poc\saga\state\StateInterface;

interface StepInterface
{
    /**
     * Полезная работа шага транзакции.
     * В случае ошибки, во всех предыдущих шагах будет выполнен rollback.
     */
    public function commit(): bool;

    /**
     * Операция полностью обратная commit.
     * После выполнения состояние приложения должно быть ровно такое же, как до начала.
     */
    public function rollback(): bool;

    /**
     * Каждый Шаг должен быть связан с Состоянием Транзакции.
     */
    public function bind(string $uuid, StateInterface $state): void;
}
