<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\poc\saga\exception\ProcessingException;
use kuaukutsu\poc\saga\tests\stub\Storage;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;
use kuaukutsu\poc\saga\tests\stub\TestTransactionRollback;
use kuaukutsu\poc\saga\TransactionRunner;
use PHPUnit\Framework\TestCase;

final class TransactionRollbackTest extends TestCase
{
    private TransactionRunner $runner;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function setUp(): void
    {
        Storage::clean();
        $this->runner = (new Container())->get(TransactionRunner::class);
    }

    public function testRollback(): void
    {
        $transaction = $this->runner->run(
            new TestTransaction()
        );

        self::assertNotEmpty($transaction->uuid);
        self::assertEquals('test', Storage::get('save'));
        self::assertEquals(1, Storage::count());
    }

    public function testFailure(): void
    {
        try {
            $this->runner->run(
                new TestTransactionRollback('rollback')
            );
        } catch (ProcessingException) {
        }

        self::assertEmpty(Storage::get('rollback'));
        self::assertEmpty(Storage::get('failure'));
        self::assertEquals(0, Storage::count());
    }
}
