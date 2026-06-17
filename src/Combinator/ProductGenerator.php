<?php

declare(strict_types=1);

namespace Dseguy\Generator\Combinator;

use Dseguy\Generator\AbstractGenerator;
use Dseguy\Generator\GeneratorInterface;

/**
 * Yields the Cartesian product of two generators as flat tuples.
 *
 * Tuples are always one level deep: if either side already yields an array
 * (e.g. from a prior Product or Repeat), its elements are spread into the
 * resulting tuple rather than nested.
 *
 * AbstractGenerator::product(...$others) chains multiple ProductGenerators
 * in sequence when more than two generators are composed.
 */
final class ProductGenerator extends AbstractGenerator
{
    public function __construct(
        private readonly GeneratorInterface $left,
        private readonly GeneratorInterface $right,
    ) {
        if ($left->isInfinite()) {
            throw new \LogicException('Cannot use an infinite source as the left operand of product().');
        }
        if ($right->isInfinite()) {
            throw new \LogicException('Cannot use an infinite source as the right operand of product().');
        }
    }

    public function getIterator(): \Generator
    {
        foreach ($this->left->getIterator() as $leftValue) {
            foreach ($this->right->getIterator() as $rightValue) {
                $left  = is_array($leftValue)  ? $leftValue  : [$leftValue];
                $right = is_array($rightValue) ? $rightValue : [$rightValue];
                yield array_merge($left, $right);
            }
        }
    }
}
