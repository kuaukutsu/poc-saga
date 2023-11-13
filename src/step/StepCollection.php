<?php

declare(strict_types=1);

namespace kuaukutsu\poc\saga\step;

use kuaukutsu\ds\collection\Collection;

/**
 * @extends Collection<Step>
 */
final class StepCollection extends Collection
{
    public function getType(): string
    {
        return Step::class;
    }
}
