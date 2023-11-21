<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\poc\saga\step\StepCollection;
use kuaukutsu\poc\saga\TransactionInterface;

final class TestStepEmpty implements TransactionInterface
{
    public function steps(): StepCollection
    {
        return new StepCollection();
    }
}
