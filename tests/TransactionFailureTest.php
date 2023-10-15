<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\poc\saga\exception\TransactionProcessingException;
use kuaukutsu\poc\saga\tests\stub\TestTransactionFailure;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\handler\TransactionRunner;

final class TransactionFailureTest extends TestCase
{
    private TransactionRunner $runner;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function setUp(): void
    {
        $this->runner = (new Container())->get(TransactionRunner::class);
    }

    public function testFailure(): void
    {
        $this->expectException(TransactionProcessingException::class);

        $transaction = $this->runner->run(
            new TestTransactionFailure()
        );

        self::assertNotEmpty($transaction->uuid);
    }
}
