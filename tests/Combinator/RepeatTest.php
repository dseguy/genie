<?php

declare(strict_types=1);

namespace Exakat\Generator\Tests\Combinator;

use Exakat\Generator\Digits;
use Exakat\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class RepeatTest extends TestCase
{
    public function testRepeat1YieldsSingleElementArrays(): void
    {
        $values = iterator_to_array(
            Digits::range(0, 2)->repeat(1)->toIterator(),
            false
        );

        self::assertSame([[0], [1], [2]], $values);
    }

    public function testRepeat2YieldsPairs(): void
    {
        $values = iterator_to_array(
            Digits::range(0, 1)->repeat(2)->toIterator(),
            false
        );

        // 2^2 = 4 pairs, repetition allowed
        self::assertSame(
            [[0, 0], [0, 1], [1, 0], [1, 1]],
            $values
        );
    }

    public function testRepeat3YieldsTriplets(): void
    {
        $values = iterator_to_array(
            Digits::range(0, 1)->repeat(3)->toIterator(),
            false
        );

        self::assertCount(8, $values); // 2^3
        self::assertSame([0, 0, 0], $values[0]);
        self::assertSame([1, 1, 1], $values[7]);
    }

    public function testRepeatAllowsRepetition(): void
    {
        $values = iterator_to_array(
            Letters::lower()->repeat(2)->toIterator(),
            false
        );

        self::assertCount(676, $values); // 26^2 — includes ['a','a'], ['b','b'], etc.
        self::assertContains(['a', 'a'], $values);
        self::assertContains(['z', 'z'], $values);
    }

    public function testThrowsWhenCountIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Letters::lower()->repeat(0);
    }

    public function testThrowsWhenCountIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Letters::lower()->repeat(-1);
    }

    public function testRepeatedGeneratorIsReusable(): void
    {
        $pairs = Digits::range(0, 2)->repeat(2);

        $first  = iterator_to_array($pairs->toIterator(), false);
        $second = iterator_to_array($pairs->toIterator(), false);

        self::assertSame($first, $second);
    }
}
