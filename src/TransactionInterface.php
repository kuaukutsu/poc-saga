<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\step\StepCollection;

interface TransactionInterface
{
    public function steps(): StepCollection;
}
