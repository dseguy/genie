<?php

declare(strict_types=1);

namespace Exakat\Generator;

/** @extends \IteratorAggregate<int, mixed> */
interface GeneratorInterface extends \IteratorAggregate
{
    public function getIterator(): \Generator;

    public function merge(GeneratorInterface ...$others): GeneratorInterface;

    public function product(GeneratorInterface ...$others): GeneratorInterface;

    public function filter(callable $predicate): GeneratorInterface;

    public function map(callable $transform): GeneratorInterface;

    public function repeat(int $n): GeneratorInterface;

    public function toIterator(): \Iterator;
}
