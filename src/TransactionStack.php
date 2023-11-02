<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use SplStack;
use Exception;
use kuaukutsu\poc\saga\step\TransactionStepInterface;

final class TransactionStack
{
    /**
     * @var SplStack<TransactionStepInterface>
     */
    private readonly SplStack $stack;

    public function __construct()
    {
        $this->stack = new SplStack();
    }

    public function push(TransactionStepInterface $step): void
    {
        $this->stack->push($step);
    }

    public function rollback(): void
    {
        foreach ($this->stack as $step) {
            try {
                $step->rollback();
            } catch (Exception) {
                continue;
            }
        }
    }
}
