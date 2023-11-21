<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\state;

use kuaukutsu\poc\saga\exception\NotFoundException;
use kuaukutsu\poc\saga\step\StepInterface;
use kuaukutsu\poc\saga\TransactionDataInterface;

/**
 * При желании можно заменить на persistent storage.
 */
final class State implements StateInterface
{
    /**
     * @var array<class-string<StepInterface>, TransactionDataInterface>
     */
    private array $state = [];

    /**
     * @var array<class-string<TransactionDataInterface>, array<class-string<StepInterface>>>
     */
    private array $index = [];

    public function set(string $stepName, TransactionDataInterface $data): void
    {
        $this->state[$stepName] = $data;
        $this->index[$data::class][] = $stepName;
    }

    public function get(string $name): TransactionDataInterface
    {
        $pointer = $this->getPointer($name);
        if ($pointer !== false) {
            return $this->getStepData($pointer);
        }

        throw new NotFoundException("[$name] Model not found.");
    }

    public function getStepData(string $stepName): TransactionDataInterface
    {
        if (array_key_exists($stepName, $this->state)) {
            return $this->state[$stepName];
        }

        throw new NotFoundException("[$stepName] Step not found.");
    }

    public function stack(): StateCollection
    {
        $collection = new StateCollection();
        foreach ($this->index as $index) {
            $pointer = end($index);
            if ($pointer !== false && array_key_exists($pointer, $this->state)) {
                $collection->attach(
                    $this->state[$pointer]
                );
            }
        }

        return $collection;
    }

    public function clean(): void
    {
        $this->state = [];
        $this->index = [];
    }

    /**
     * @param class-string<TransactionDataInterface> $name
     * @return class-string<StepInterface>|false
     */
    private function getPointer(string $name): string | false
    {
        if (array_key_exists($name, $this->index)) {
            $index = $this->index[$name];
            return end($index);
        }

        return false;
    }
}
