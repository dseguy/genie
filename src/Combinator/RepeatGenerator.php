<?php

declare(strict_types=1);

namespace Exakat\Generator\Combinator;

use Exakat\Generator\AbstractGenerator;
use Exakat\Generator\GeneratorInterface;

/**
 * Yields all n-length sequences drawn from a single generator (G^n).
 * Each yielded value is a flat list, including for n=1.
 * Repetition is allowed: the same value may appear multiple times in one tuple.
 *
 * Letters::lower()->repeat(2) yields ['a','a'], ['a','b'], …, ['z','z']  (676 values)
 * Letters::lower()->repeat(1) yields ['a'], ['b'], …, ['z']              (26 values)
 */
final class RepeatGenerator extends AbstractGenerator
{
    public function __construct(
        private readonly GeneratorInterface $source,
        private readonly int $n,
    ) {
        if ($n <= 0) {
            throw new \InvalidArgumentException(
                "Repeat count must be > 0, got {$n}."
            );
        }
    }

    public function getIterator(): \Generator
    {
        yield from $this->generate([], 0);
    }

    /**
     * @param list<mixed> $current
     * @return \Generator<int, list<mixed>, void, void>
     */
    private function generate(array $current, int $depth): \Generator
    {
        if ($depth === $this->n) {
            yield $current;
            return;
        }

        foreach ($this->source->getIterator() as $value) {
            yield from $this->generate([...$current, $value], $depth + 1);
        }
    }
}
