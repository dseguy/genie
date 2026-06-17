<?php

declare(strict_types=1);

namespace Dseguy\Generator\Combinator;

use Dseguy\Generator\AbstractGenerator;
use Dseguy\Generator\GeneratorInterface;

/**
 * Yields at most $n values from the upstream source, then stops.
 * If the source has fewer than $n values, all of them are yielded without error.
 * Seals an Infinite Source: isInfinite() always returns false.
 */
final class TakeGenerator extends AbstractGenerator
{
    public function __construct(
        private readonly GeneratorInterface $source,
        private readonly int $n,
    ) {
        if ($n < 1) {
            throw new \InvalidArgumentException(
                "Take count must be >= 1, got {$n}."
            );
        }
    }

    public function isInfinite(): bool
    {
        return false;
    }

    public function getIterator(): \Generator
    {
        $count = 0;
        foreach ($this->source->getIterator() as $value) {
            yield $value;
            if (++$count >= $this->n) {
                return;
            }
        }
    }
}
