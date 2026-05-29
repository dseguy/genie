<?php

declare(strict_types=1);

namespace Dseguy\Generator;

final class Booleans extends AbstractGenerator
{
    private function __construct(private readonly bool $includeNull) {}

    public static function values(): self
    {
        return new self(false);
    }

    public static function withNull(): self
    {
        return new self(true);
    }

    public function getIterator(): \Generator
    {
        yield true;
        yield false;

        if ($this->includeNull) {
            yield null;
        }
    }
}
