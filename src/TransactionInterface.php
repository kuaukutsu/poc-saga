<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use kuaukutsu\poc\saga\dto\TransactionStepCollection;

interface TransactionInterface
{
    public function steps(): TransactionStepCollection;

    public function getCommitCallback(): ?CommitCallback;

    public function getRollbackCallback(): ?RollbackCallback;
}
