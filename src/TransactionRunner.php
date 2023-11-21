<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga;

use Throwable;
use Ramsey\Uuid\UuidFactory;
use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\exception\ProcessingException;
use kuaukutsu\poc\saga\exception\StepFactoryException;
use kuaukutsu\poc\saga\state\State;
use kuaukutsu\poc\saga\step\StepStack;
use kuaukutsu\poc\saga\step\StepFactory;

final class TransactionRunner
{
    /**
     * @var non-empty-string
     */
    private readonly string $uuid;

    public function __construct(
        private readonly State $state,
        private readonly StepStack $stack,
        private readonly StepFactory $stepFactory,
        UuidFactory $uuidFactory,
    ) {
        $this->uuid = $uuidFactory->uuid7()->toString();
    }

    /**
     * @throws NotFoundException Если нет сконфигурированных шагов.
     * @throws StepFactoryException Если транзакция потерпела фиаско.
     * @throws ProcessingException Если транзакция потерпела фиаско.
     */
    public function run(TransactionInterface $transaction): TransactionResult
    {
        $stepQueue = $this->stepFactory->createQueue($this->uuid, $transaction);
        foreach ($stepQueue as $step) {
            try {
                $step->bind($this->uuid, $this->state);
                $isSuccess = $step->commit();
            } catch (Throwable $exception) {
                $this->stack->rollback();
                $this->state->clean();

                throw new ProcessingException($this->uuid, $step::class, $exception);
            }

            if ($isSuccess === false) {
                $step->rollback();
                $this->stack->rollback();
                $this->state->clean();

                throw new ProcessingException($this->uuid, $step::class);
            }

            $this->stack->push($step);
        }

        return new TransactionResult($this->uuid, $this->state->stack());
    }
}
