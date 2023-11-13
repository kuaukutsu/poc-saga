<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\step\Step;
use kuaukutsu\poc\saga\step\StepCollection;
use kuaukutsu\poc\saga\TransactionInterface;

final class TestTransactionRollback implements TransactionInterface
{
    public function __construct(private readonly string $name)
    {
    }

    public function steps(): StepCollection
    {
        return new StepCollection(
            new Step(
                SaveTransactionStep::class,
                [
                    'name' => $this->name,
                ]
            ),
            new Step(
                FailureTransactionStep::class,
                [
                    'name' => 'failure',
                ]
            ),
        );
    }
}
