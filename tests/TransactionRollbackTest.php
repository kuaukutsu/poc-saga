<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use Psr\Container\ContainerExceptionInterface;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\exception\ProcessingException;
use kuaukutsu\poc\saga\tests\stub\Storage;
use kuaukutsu\poc\saga\tests\stub\TestTransactionRollback;
use kuaukutsu\poc\saga\TransactionRunner;

final class TransactionRollbackTest extends TestCase
{
    private TransactionRunner $runner;

    /**
     * @throws ContainerExceptionInterface
     */
    protected function setUp(): void
    {
        Storage::clean();
        $this->runner = (new Container())->get(TransactionRunner::class);
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
