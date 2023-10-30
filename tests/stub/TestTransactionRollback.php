<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\step\TransactionStep;
use kuaukutsu\poc\saga\step\TransactionStepCollection;
use kuaukutsu\poc\saga\TransactionInterface;

final class TestTransactionRollback implements TransactionInterface
{
    public function __construct(private readonly string $name)
    {
    }

    public function steps(): TransactionStepCollection
    {
        return new TransactionStepCollection(
            new TransactionStep(
                SaveStep::class,
                [
                    'name' => $this->name,
                ]
            ),
            new TransactionStep(
                FailureStep::class,
                [
                    'name' => 'failure',
                ]
            ),
        );
    }
}
