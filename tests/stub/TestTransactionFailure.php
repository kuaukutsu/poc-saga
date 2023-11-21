<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\step\Step;
use kuaukutsu\poc\saga\step\StepCollection;
use kuaukutsu\poc\saga\TransactionInterface;

final class TestTransactionFailure implements TransactionInterface
{
    public function steps(): StepCollection
    {
        return new StepCollection(
            new Step(
                OneTransactionStep::class,
                [
                    'name' => 'one',
                ]
            ),
            new Step(
                TwoTransactionStep::class,
                [
                    'name' => 'two',
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
