<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;
use kuaukutsu\poc\saga\tests\stub\TestTransactionData;
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

        self::assertCount(1, $transaction->state);

        /** @var TestTransactionData|null $model */
        $model = $transaction->state
            ->get(TestTransactionData::class);

        self::assertNotEmpty($model);
        self::assertEquals('two', $model->name);
    }
}
