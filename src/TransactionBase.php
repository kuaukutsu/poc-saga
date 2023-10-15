<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

abstract class TransactionBase implements TransactionInterface
{
    private ?CommitCallback $commitCallback = null;

    private ?RollbackCallback $rollbackCallback = null;

    public function getCommitCallback(): ?CommitCallback
    {
        return $this->commitCallback;
    }

    public function getRollbackCallback(): ?RollbackCallback
    {
        return $this->rollbackCallback;
    }

    final protected function setCommitCallback(CommitCallback $callback): void
    {
        $this->commitCallback = $callback;
    }

    final protected function setRollbackCallback(RollbackCallback $callback): void
    {
        $this->rollbackCallback = $callback;
    }
}
