<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use Exception;
use SplStack;

final class StepStack
{
    /**
     * @var SplStack<StepInterface>
     */
    private readonly SplStack $stack;

    public function __construct()
    {
        $this->stack = new SplStack();
    }

    public function push(StepInterface $step): void
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
