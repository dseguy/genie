<?php

declare(strict_types=1);

namespace Dseguy\Generator;

final class FromCallable extends AbstractGenerator
{
    /** @var callable(): \Traversable<mixed> */
    private $factory;

    /**
     * @param callable(): \Traversable<mixed> $factory
     *
     * WARNING: the factory is invoked on every iteration, including once per
     * left-hand value inside product() and once per depth level inside repeat().
     * Passing a factory that wraps an infinite source is safe only with
     * combinators that do not require full traversal (filter, map, merge).
     * Using an infinite source with product() or repeat() will loop forever.
     */
    private function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /** @param callable(): \Traversable<mixed> $factory */
    public static function of(callable $factory): self
    {
        return new self($factory);
    }

    public function getIterator(): \Generator
    {
        foreach (($this->factory)() as $value) {
            yield $value;
        }
    }
}
