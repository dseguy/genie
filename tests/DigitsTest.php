<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Digits;
use PHPUnit\Framework\TestCase;

final class DigitsTest extends TestCase
{
    public function testAllYields0To9(): void
    {
        $values = iterator_to_array(Digits::all()->toIterator(), false);
        self::assertSame([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], $values);
    }

    public function testRangeIsInclusive(): void
    {
        $values = iterator_to_array(Digits::range(2, 5)->toIterator(), false);
        self::assertSame([2, 3, 4, 5], $values);
    }

    public function testRangeWithStep(): void
    {
        $values = iterator_to_array(Digits::range(0, 10, 2)->toIterator(), false);
        self::assertSame([0, 2, 4, 6, 8, 10], $values);
    }

    public function testSingleElementRange(): void
    {
        $values = iterator_to_array(Digits::range(7, 7)->toIterator(), false);
        self::assertSame([7], $values);
    }

    public function testThrowsWhenStartExceedsEnd(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Digits::range(5, 1);
    }

    public function testThrowsWhenStepIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Digits::range(0, 10, 0);
    }

    public function testThrowsWhenStepIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Digits::range(0, 10, -1);
    }

    public function testGeneratorIsReusable(): void
    {
        $digits = Digits::range(1, 3);

        $first  = iterator_to_array($digits->toIterator(), false);
        $second = iterator_to_array($digits->toIterator(), false);

        self::assertSame($first, $second);
    }
}
