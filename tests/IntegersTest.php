<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Integers;
use Dseguy\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class IntegersTest extends TestCase
{
    public function testNaturalStartsAtZero(): void
    {
        $values = iterator_to_array(Integers::natural()->take(5)->toIterator(), false);

        self::assertSame([0, 1, 2, 3, 4], $values);
    }

    public function testFromStartsAtGivenOffset(): void
    {
        $values = iterator_to_array(Integers::from(5)->take(4)->toIterator(), false);

        self::assertSame([5, 6, 7, 8], $values);
    }

    public function testFromWithNegativeStart(): void
    {
        $values = iterator_to_array(Integers::from(-3)->take(5)->toIterator(), false);

        self::assertSame([-3, -2, -1, 0, 1], $values);
    }

    public function testAllInterleavePositiveFirst(): void
    {
        $values = iterator_to_array(Integers::all()->take(7)->toIterator(), false);

        self::assertSame([0, 1, -1, 2, -2, 3, -3], $values);
    }

    public function testNaturalIsInfinite(): void
    {
        self::assertTrue(Integers::natural()->isInfinite());
    }

    public function testFromIsInfinite(): void
    {
        self::assertTrue(Integers::from(42)->isInfinite());
    }

    public function testAllIsInfinite(): void
    {
        self::assertTrue(Integers::all()->isInfinite());
    }

    public function testProductThrowsOnInfiniteSource(): void
    {
        $this->expectException(\LogicException::class);

        Integers::natural()->product(Letters::lower());
    }

    public function testRepeatThrowsOnInfiniteSource(): void
    {
        $this->expectException(\LogicException::class);

        Integers::natural()->repeat(2);
    }

    public function testProductThrowsWhenChainedThroughMap(): void
    {
        $this->expectException(\LogicException::class);

        Integers::natural()->map(fn($n) => $n * 2)->product(Letters::lower());
    }

    public function testNaturalIsReusable(): void
    {
        $gen = Integers::natural()->take(3);

        $first  = iterator_to_array($gen->toIterator(), false);
        $second = iterator_to_array($gen->toIterator(), false);

        self::assertSame($first, $second);
    }
}
