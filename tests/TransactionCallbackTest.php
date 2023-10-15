<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\poc\saga\CommitCallback;
use kuaukutsu\poc\saga\dto\TransactionStateCollection;
use kuaukutsu\poc\saga\tests\stub\OneStep;
use kuaukutsu\poc\saga\tests\stub\TwoStep;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\handler\TransactionRunner;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;

final class TransactionCallbackTest extends TestCase
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
            new TestTransaction(),
            new CommitCallback(
                static function (TransactionStateCollection $storage): void {
                    $list = $storage->toArrayRecursive();
                    foreach ($list as $item) {
                        self::assertArrayHasKey('step', $item);
                        self::assertArrayHasKey('data', $item);

                        $data = $item['data'];
                        self::assertArrayHasKey('name', $data);

                        switch ($item['step']) {
                            case OneStep::class:
                                self::assertEquals('one', $data['name']);
                                break;
                            case TwoStep::class:
                                self::assertEquals('two', $data['name']);
                        }
                    }
                }
            )
        );

        self::assertNotEmpty($transaction->uuid);
    }
}
