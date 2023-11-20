<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\tests\stub\OneTransactionStep;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;
use kuaukutsu\poc\saga\tests\stub\TestTransactionData;
use kuaukutsu\poc\saga\tests\stub\TwoTransactionStep;
use kuaukutsu\poc\saga\TransactionRunner;

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
            ->getStepData(OneTransactionStep::class)
            ->toArray();

        self::assertArrayHasKey('name', $stateOne);
        self::assertArrayHasKey('datetime', $stateOne);
        self::assertEquals('one', $stateOne['name']);

        $stateTwo = $transaction->state
            ->getStepData(TwoTransactionStep::class)
            ->toArray();

        self::assertArrayHasKey('name', $stateTwo);
        self::assertArrayHasKey('datetime', $stateTwo);
        self::assertEquals('two', $stateTwo['name']);

        /** @var TestTransactionData|null $model */
        $model = $transaction->state
            ->getData(TestTransactionData::class);

        self::assertNotEmpty($model);
        self::assertEquals('two', $model->name);
        self::assertEquals($stateTwo, $model->toArray());
    }
}
