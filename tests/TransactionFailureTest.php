<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\poc\saga\exception\ProcessingException;
use kuaukutsu\poc\saga\state\StepStateCollection;
use kuaukutsu\poc\saga\tests\stub\TestTransactionException;
use kuaukutsu\poc\saga\tests\stub\TestTransactionFailure;
use kuaukutsu\poc\saga\tools\TransactionRollbackCallback;
use kuaukutsu\poc\saga\TransactionRunner;
use PHPUnit\Framework\TestCase;

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
        $this->expectException(ProcessingException::class);

        $this->runner->run(
            new TestTransactionFailure(),
            new TransactionRollbackCallback(
                static function (string $uuid, StepStateCollection $storage): void {
                    // в стеке первые два шага, в том числе Failure
                    self::assertCount(2, $storage);
                }
            )
        );
    }

    public function testException(): void
    {
        $this->expectException(ProcessingException::class);

        $this->runner->run(
            new TestTransactionException(),
            new TransactionRollbackCallback(
                static function (string $uuid, StepStateCollection $storage): void {
                    // в стеке только первый шаг, так как второй Exception
                    self::assertCount(1, $storage);
                }
            )
        );
    }
}
