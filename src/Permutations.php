<?php

declare(strict_types=1);

namespace Dseguy\Generator;

/**
 * Yields all ordered arrangements of $length distinct characters drawn from $charset.
 * No character repeats within a single yielded value.
 *
 * Permutations::of(2, 'abc') yields: 'ab', 'ac', 'ba', 'bc', 'ca', 'cb'
 *
 * This is distinct from repeat(), which allows character repetition.
 */
final class Permutations extends AbstractGenerator
{
    /** @var list<string> */
    private readonly array $chars;

    private function __construct(private readonly int $length, string $charset)
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException(
                "Length must be > 0, got {$length}."
            );
        }

        $chars = str_split($charset);

        if (count($chars) < $length) {
            throw new \InvalidArgumentException(
                "Charset must contain at least {$length} distinct character(s) " .
                "for permutations of length {$length}, got " . count($chars) . '.'
            );
        }

        $this->chars = $chars;
    }

    public static function of(int $length, string $charset): self
    {
        return new self($length, $charset);
    }

    public function getIterator(): \Generator
    {
        yield from $this->permute([], $this->chars);
    }

    /**
     * @param list<string> $current
     * @param list<string> $remaining
     * @return \Generator<int, string, void, void>
     */
    private function permute(array $current, array $remaining): \Generator
    {
        if (count($current) === $this->length) {
            yield implode('', $current);
            return;
        }

        foreach ($remaining as $index => $char) {
            $next = $remaining;
            array_splice($next, $index, 1);
            yield from $this->permute([...$current, $char], $next);
        }
    }
}
