<?php

declare(strict_types=1);

namespace Dseguy\Generator;

final class Integers extends AbstractGenerator
{
    private function __construct(
        private readonly int $start,
        private readonly bool $interleaved,
    ) {}

    public static function natural(): self
    {
        return new self(0, false);
    }

    public static function from(int $start): self
    {
        return new self($start, false);
    }

    public static function all(): self
    {
        return new self(0, true);
    }

    public function isInfinite(): bool
    {
        return true;
    }

    public function getIterator(): \Generator
    {
        if ($this->interleaved) {
            yield 0;
            for ($i = 1; ; $i++) {
                yield $i;
                yield -$i;
            }
        } else {
            for ($i = $this->start; ; $i++) {
                yield $i;
            }
        }
    }
}
