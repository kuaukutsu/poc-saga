<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\tests;

use DI\Container;
use Psr\Container\ContainerExceptionInterface;
use PHPUnit\Framework\TestCase;
use kuaukutsu\poc\saga\tests\stub\TestStepEmpty;
use kuaukutsu\poc\saga\tests\stub\TestStepFailure;
use kuaukutsu\poc\saga\tests\stub\TestTransactionException;
use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\exception\ProcessingException;
use kuaukutsu\poc\saga\exception\StepFactoryException;
use kuaukutsu\poc\saga\TransactionRunner;

final class TransactionFailureTest extends TestCase
{
    private TransactionRunner $runner;

    /**
     * @throws ContainerExceptionInterface
     */
    protected function setUp(): void
    {
        $this->runner = (new Container())->get(TransactionRunner::class);
    }

    public function testStepEmpty(): void
    {
        $this->expectException(NotFoundException::class);

        $this->runner->run(
            new TestStepEmpty()
        );
    }

    public function testStepFailure(): void
    {
        $this->expectException(StepFactoryException::class);

        $this->runner->run(
            new TestStepFailure()
        );
    }

    public function testException(): void
    {
        $this->expectException(ProcessingException::class);

        $this->runner->run(
            new TestTransactionException()
        );
    }
}
