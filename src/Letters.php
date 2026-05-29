<?php

declare(strict_types=1);

namespace Dseguy\Generator;

final class Letters extends AbstractGenerator
{
    private const string LOWER = 'abcdefghijklmnopqrstuvwxyz';
    private const string UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const string ALL   = self::LOWER . self::UPPER;

    private function __construct(private readonly string $chars) {}

    public static function lower(): self
    {
        return new self(self::LOWER);
    }

    public static function upper(): self
    {
        return new self(self::UPPER);
    }

    /**
     * Yields a–z then A–Z, equivalent to Letters::lower()->merge(Letters::upper()).
     */
    public static function all(): self
    {
        return new self(self::ALL);
    }

    public function getIterator(): \Generator
    {
        foreach (str_split($this->chars) as $char) {
            yield $char;
        }
    }
}
