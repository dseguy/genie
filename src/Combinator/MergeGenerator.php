<?php

declare(strict_types=1);

namespace Dseguy\Generator\Combinator;

use Dseguy\Generator\AbstractGenerator;
use Dseguy\Generator\GeneratorInterface;

/**
 * Chains generators end-to-end into a single sequence.
 * Values from all sources are yielded in order; types are not enforced.
 */
final class MergeGenerator extends AbstractGenerator
{
    /** @var list<GeneratorInterface> */
    private readonly array $sources;

    public function __construct(GeneratorInterface ...$sources)
    {
        $this->sources = array_values($sources);
    }

    public function getIterator(): \Generator
    {
        foreach ($this->sources as $source) {
            yield from $source->getIterator();
        }
    }
}
