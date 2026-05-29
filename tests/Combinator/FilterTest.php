<?php

declare(strict_types=1);

namespace Exakat\Generator\Tests\Combinator;

use Exakat\Generator\Digits;
use Exakat\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    public function testFilterKeepsMatchingValues(): void
    {
        $values = iterator_to_array(
            Digits::range(1, 10)->filter(fn($n) => $n % 2 === 0)->toIterator(),
            false
        );

        self::assertSame([2, 4, 6, 8, 10], $values);
    }

    public function testFilterRemovesAllNonMatching(): void
    {
        $values = iterator_to_array(
            Digits::range(1, 5)->filter(fn($n) => $n > 10)->toIterator(),
            false
        );

        self::assertSame([], $values);
    }

    public function testFilterOnMixedMergeSequence(): void
    {
        // Keep only strings (letters), discard integers (digits)
        $values = iterator_to_array(
            Letters::lower()->merge(Digits::all())->filter(fn($v) => is_string($v))->toIterator(),
            false
        );

        self::assertCount(26, $values);
        self::assertContainsOnly('string', $values);
    }

    public function testFilterOnProductTuples(): void
    {
        // Keep only pairs where letter is 'a'
        $values = iterator_to_array(
            Letters::lower()->product(Digits::all())->filter(fn($t) => $t[0] === 'a')->toIterator(),
            false
        );

        self::assertCount(10, $values);

        foreach ($values as $tuple) {
            self::assertSame('a', $tuple[0]);
        }
    }

    public function testFilteredGeneratorIsReusable(): void
    {
        $evens = Digits::range(1, 10)->filter(fn($n) => $n % 2 === 0);

        $first  = iterator_to_array($evens->toIterator(), false);
        $second = iterator_to_array($evens->toIterator(), false);

        self::assertSame($first, $second);
    }
}
