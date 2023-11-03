<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use kuaukutsu\poc\saga\TransactionCommitCallback;
use kuaukutsu\poc\saga\tests\stub\OneStep;
use kuaukutsu\poc\saga\tests\stub\TestTransaction;
use kuaukutsu\poc\saga\tests\stub\TwoStep;
use kuaukutsu\poc\saga\TransactionResult;
use kuaukutsu\poc\saga\TransactionRunner;
use PHPUnit\Framework\TestCase;

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
            new TransactionCommitCallback(
                static function (string $uuid, TransactionResult $result): void {
                    self::assertNotEmpty($result->state->get(OneStep::class));

                    $list = $result->state->toArrayRecursive();
                    foreach ($list as $step => $data) {
                        self::assertArrayHasKey('name', $data);
                        switch ($step) {
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
