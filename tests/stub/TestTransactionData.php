<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests\stub;

use kuaukutsu\ds\dto\DtoBase;
use kuaukutsu\poc\saga\state\TransactionDataInterface;

final class TestTransactionData extends DtoBase implements TransactionDataInterface
{
    public string $name;

    public string $datetime;
}
