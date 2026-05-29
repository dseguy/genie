<?php

declare(strict_types=1);

namespace Exakat\Generator\Tests\Combinator;

use Exakat\Generator\Digits;
use Exakat\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class MapTest extends TestCase
{
    public function testMapTransformsScalars(): void
    {
        $values = iterator_to_array(
            Letters::lower()->map(fn($c) => strtoupper($c))->toIterator(),
            false
        );

        self::assertCount(26, $values);
        self::assertSame('A', $values[0]);
        self::assertSame('Z', $values[25]);
    }

    public function testMapTransformsIntegers(): void
    {
        $values = iterator_to_array(
            Digits::range(1, 5)->map(fn($n) => $n * $n)->toIterator(),
            false
        );

        self::assertSame([1, 4, 9, 16, 25], $values);
    }

    public function testMapOnProductReceivesArray(): void
    {
        $values = iterator_to_array(
            Letters::lower()->product(Digits::all())
                ->map(fn($t) => $t[0] . $t[1])
                ->toIterator(),
            false
        );

        self::assertCount(260, $values);
        self::assertSame('a0', $values[0]);
        self::assertSame('a9', $values[9]);
        self::assertSame('b0', $values[10]);
    }

    public function testMapCanChangeType(): void
    {
        $values = iterator_to_array(
            Digits::range(0, 2)->map(fn($n) => (bool) $n)->toIterator(),
            false
        );

        self::assertSame([false, true, true], $values);
    }

    public function testMappedGeneratorIsReusable(): void
    {
        $upper = Letters::lower()->map(fn($c) => strtoupper($c));

        $first  = iterator_to_array($upper->toIterator(), false);
        $second = iterator_to_array($upper->toIterator(), false);

        self::assertSame($first, $second);
    }
}
