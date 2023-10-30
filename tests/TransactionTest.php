<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\poc\saga\tests\stub\OneStep;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;
use kuaukutsu\poc\saga\tests\stub\TwoStep;
use kuaukutsu\poc\saga\TransactionRunner;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
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

    public function testRunner(): void
    {
        $transaction = $this->runner->run(
            new TestTransaction()
        );

        self::assertNotEmpty($transaction->uuid);
    }

    public function testState(): void
    {
        $transaction = $this->runner->run(
            new TestTransaction()
        );

        self::assertCount(2, $transaction->state);

        /** @var array{"name": string, "datetime": string} $stateOne */
        $stateOne = $transaction->state
            ->getData(OneStep::class)
            ->toArray();

        self::assertArrayHasKey('name', $stateOne);
        self::assertArrayHasKey('datetime', $stateOne);
        self::assertEquals('one', $stateOne['name']);

        $stateTwo = $transaction->state
            ->getData(TwoStep::class)
            ->toArray();

        self::assertArrayHasKey('name', $stateTwo);
        self::assertArrayHasKey('datetime', $stateTwo);
        self::assertEquals('two', $stateTwo['name']);
    }
}
