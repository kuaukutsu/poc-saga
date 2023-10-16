<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\step\TransactionStep;
use kuaukutsu\poc\saga\step\TransactionStepCollection;
use kuaukutsu\poc\saga\TransactionBase;

final class TestTransactionFailure extends TransactionBase
{
    public function steps(): TransactionStepCollection
    {
        return new TransactionStepCollection(
            new TransactionStep(
                OneStep::class,
                [
                    'name' => 'one',
                ]
            ),
            new TransactionStep(
                FailureStep::class,
                [
                    'name' => 'failure',
                ]
            ),
            new TransactionStep(
                TwoStep::class,
                [
                    'name' => 'two',
                ]
            ),
        );
    }
}
