<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests\Combinator;

use Dseguy\Generator\Digits;
use Dseguy\Generator\Integers;
use Dseguy\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class TakeTest extends TestCase
{
    public function testTakeTruncatesInfiniteSource(): void
    {
        $values = iterator_to_array(Integers::natural()->take(5)->toIterator(), false);

        self::assertSame([0, 1, 2, 3, 4], $values);
    }

    public function testTakeStopsSilentlyOnShortSource(): void
    {
        $values = iterator_to_array(Digits::all()->take(100)->toIterator(), false);

        self::assertCount(10, $values);
    }

    public function testTakeZeroThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Integers::natural()->take(0);
    }

    public function testTakeNegativeThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Integers::natural()->take(-1);
    }

    public function testTakeSealsInfiniteForProduct(): void
    {
        $values = iterator_to_array(
            Integers::natural()->take(3)->product(Letters::lower()->take(2))->toIterator(),
            false
        );

        self::assertSame([[0, 'a'], [0, 'b'], [1, 'a'], [1, 'b'], [2, 'a'], [2, 'b']], $values);
    }

    public function testTakeIsNotInfinite(): void
    {
        self::assertFalse(Integers::natural()->take(10)->isInfinite());
    }

    public function testTakeOnFilteredInfiniteSource(): void
    {
        $values = iterator_to_array(
            Integers::natural()->filter(fn($n) => $n % 2 === 0)->take(5)->toIterator(),
            false
        );

        self::assertSame([0, 2, 4, 6, 8], $values);
    }

    public function testTakeIsReusable(): void
    {
        $gen = Integers::natural()->take(4);

        $first  = iterator_to_array($gen->toIterator(), false);
        $second = iterator_to_array($gen->toIterator(), false);

        self::assertSame($first, $second);
    }
}
