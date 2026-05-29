<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class LettersTest extends TestCase
{
    public function testLowerYields26Characters(): void
    {
        $values = iterator_to_array(Letters::lower()->toIterator(), false);
        self::assertCount(26, $values);
        self::assertSame('a', $values[0]);
        self::assertSame('z', $values[25]);
    }

    public function testUpperYields26Characters(): void
    {
        $values = iterator_to_array(Letters::upper()->toIterator(), false);
        self::assertCount(26, $values);
        self::assertSame('A', $values[0]);
        self::assertSame('Z', $values[25]);
    }

    public function testAllYieldsLowerThenUpper(): void
    {
        $all   = iterator_to_array(Letters::all()->toIterator(), false);
        $lower = iterator_to_array(Letters::lower()->toIterator(), false);
        $upper = iterator_to_array(Letters::upper()->toIterator(), false);

        self::assertSame(array_merge($lower, $upper), $all);
        self::assertCount(52, $all);
    }

    public function testGeneratorIsReusable(): void
    {
        $letters = Letters::lower();

        $first  = iterator_to_array($letters->toIterator(), false);
        $second = iterator_to_array($letters->toIterator(), false);

        self::assertSame($first, $second);
    }
}
