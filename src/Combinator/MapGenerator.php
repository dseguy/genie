<?php

declare(strict_types=1);

namespace Dseguy\Generator\Combinator;

use Dseguy\Generator\AbstractGenerator;
use Dseguy\Generator\GeneratorInterface;

/**
 * Applies a transform to every value in the sequence.
 * The transform receives whatever the upstream yields (scalar or array).
 */
final class MapGenerator extends AbstractGenerator
{
    public function __construct(
        private readonly GeneratorInterface $source,
        private readonly \Closure $transform,
    ) {}

    public function getIterator(): \Generator
    {
        foreach ($this->source->getIterator() as $value) {
            yield ($this->transform)($value);
        }
    }
}
