<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\step\TransactionStepCollection;

interface TransactionInterface
{
    public function steps(): TransactionStepCollection;
}
