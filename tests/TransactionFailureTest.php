<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\tests\stub\TestTransactionException;
use kuaukutsu\poc\saga\tests\stub\TestTransactionFailure;
use kuaukutsu\poc\saga\dto\TransactionStateCollection;
use kuaukutsu\poc\saga\exception\TransactionProcessingException;
use kuaukutsu\poc\saga\handler\TransactionRunner;
use kuaukutsu\poc\saga\RollbackCallback;

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

        $this->runner->run(
            new TestTransactionFailure(),
            new RollbackCallback(
                static function (TransactionStateCollection $storage): void {
                    // в стеке первые два шага, в том числе Failure
                    self::assertCount(2, $storage);
                }
            )
        );
    }

    public function testException(): void
    {
        $this->expectException(TransactionProcessingException::class);

        $this->runner->run(
            new TestTransactionException(),
            new RollbackCallback(
                static function (TransactionStateCollection $storage): void {
                    // в стеке только первый шаг, так как второй Exception
                    self::assertCount(1, $storage);
                }
            )
        );
    }
}
