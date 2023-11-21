<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use RuntimeException;
use kuaukutsu\poc\saga\step\Step;
use kuaukutsu\poc\saga\step\StepCollection;
use kuaukutsu\poc\saga\TransactionInterface;

final class TestStepFailure implements TransactionInterface
{
    public function steps(): StepCollection
    {
        return new StepCollection(
            new Step(
                RuntimeException::class,
                [
                    'name' => 'one',
                ]
            ),
        );
    }
}
