<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\dto\TransactionStepCollection;
use kuaukutsu\poc\saga\dto\TransactionStepDto;
use kuaukutsu\poc\saga\TransactionBase;

final class TestTransactionException extends TransactionBase
{
    public function steps(): TransactionStepCollection
    {
        return new TransactionStepCollection(
            TransactionStepDto::hydrate(
                [
                    'class' => OneStep::class,
                    'params' => [
                        'name' => 'one',
                    ],
                ]
            ),
            TransactionStepDto::hydrate(
                [
                    'class' => ExceptionStep::class,
                    'params' => [
                        'name' => 'exception',
                    ],
                ]
            ),
            TransactionStepDto::hydrate(
                [
                    'class' => TwoStep::class,
                    'params' => [
                        'name' => 'two',
                    ],
                ]
            ),
        );
    }
}
