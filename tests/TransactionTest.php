<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use Psr\Container\ContainerExceptionInterface;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;
use kuaukutsu\poc\saga\TransactionRunner;

final class TransactionTest extends TestCase
{
    private TransactionRunner $runner;

    /**
     * @throws ContainerExceptionInterface
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
}
