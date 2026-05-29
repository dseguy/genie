<?php

declare(strict_types=1);

namespace Exakat\Generator;

use Exakat\Generator\Combinator\FilterGenerator;
use Exakat\Generator\Combinator\MapGenerator;
use Exakat\Generator\Combinator\MergeGenerator;
use Exakat\Generator\Combinator\ProductGenerator;
use Exakat\Generator\Combinator\RepeatGenerator;

abstract class AbstractGenerator implements GeneratorInterface
{
    /** @return \Generator<int, mixed, void, void> */
    abstract public function getIterator(): \Generator;

    public function merge(GeneratorInterface ...$others): GeneratorInterface
    {
        return new MergeGenerator($this, ...$others);
    }

    public function product(GeneratorInterface ...$others): GeneratorInterface
    {
        $result = $this;
        foreach ($others as $other) {
            $result = new ProductGenerator($result, $other);
        }

        return $result;
    }

    public function filter(callable $predicate): GeneratorInterface
    {
        return new FilterGenerator($this, \Closure::fromCallable($predicate));
    }

    public function map(callable $transform): GeneratorInterface
    {
        return new MapGenerator($this, \Closure::fromCallable($transform));
    }

    public function repeat(int $n): GeneratorInterface
    {
        return new RepeatGenerator($this, $n);
    }

    public function toIterator(): \Iterator
    {
        return $this->getIterator();
    }
}
