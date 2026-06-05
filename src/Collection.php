<?php

declare(strict_types=1);

namespace Dseguy\Generator;

final class Collection extends AbstractGenerator
{
    /** @var list<mixed> */
    private readonly array $items;

    /** @param array<mixed> $items */
    private function __construct(array $items)
    {
        $this->items = array_values($items);
    }

    /** @param array<mixed> $items */
    public static function of(array $items): self
    {
        return new self($items);
    }

    public function getIterator(): \Generator
    {
        foreach ($this->items as $item) {
            yield $item;
        }
    }
}
