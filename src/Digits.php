<?php

declare(strict_types=1);

namespace Exakat\Generator;

final class Digits extends AbstractGenerator
{
    private function __construct(
        private readonly int $start,
        private readonly int $end,
        private readonly int $step,
    ) {
        if ($start > $end) {
            throw new \InvalidArgumentException(
                "Start ({$start}) must be <= end ({$end})."
            );
        }

        if ($step <= 0) {
            throw new \InvalidArgumentException(
                "Step must be > 0, got {$step}."
            );
        }
    }

    public static function range(int $start, int $end, int $step = 1): self
    {
        return new self($start, $end, $step);
    }

    public static function all(): self
    {
        return new self(0, 9, 1);
    }

    public function getIterator(): \Generator
    {
        for ($i = $this->start; $i <= $this->end; $i += $this->step) {
            yield $i;
        }
    }
}
