<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use Psr\Container\ContainerExceptionInterface;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\tests\stub\TestFailureData;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;
use kuaukutsu\poc\saga\tests\stub\TestTransactionData;
use kuaukutsu\poc\saga\TransactionRunner;

final class TransactionStateTest extends TestCase
{
    private TransactionRunner $runner;

    /**
     * @throws ContainerExceptionInterface
     */
    protected function setUp(): void
    {
        $this->runner = (new Container())->get(TransactionRunner::class);
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

    public function testNotFoundState(): void
    {
        $transaction = $this->runner->run(
            new TestTransaction()
        );

        self::assertEmpty(
            $transaction->state->get(TestFailureData::class)
        );
    }
}
