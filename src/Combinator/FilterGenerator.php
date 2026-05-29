<?php

declare(strict_types=1);

namespace Exakat\Generator\Combinator;

use Exakat\Generator\AbstractGenerator;
use Exakat\Generator\GeneratorInterface;

/**
 * Yields only values for which the predicate returns true.
 * The predicate receives whatever the upstream yields (scalar or array).
 */
final class FilterGenerator extends AbstractGenerator
{
    public function __construct(
        private readonly GeneratorInterface $source,
        private readonly \Closure $predicate,
    ) {}

    public function getIterator(): \Generator
    {
        foreach ($this->source->getIterator() as $value) {
            if (($this->predicate)($value)) {
                yield $value;
            }
        }
    }
}
