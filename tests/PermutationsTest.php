<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Permutations;
use PHPUnit\Framework\TestCase;

final class PermutationsTest extends TestCase
{
    public function testLength2Over3CharCharset(): void
    {
        $values = iterator_to_array(Permutations::of(2, 'abc')->toIterator(), false);

        self::assertSame(
            ['ab', 'ac', 'ba', 'bc', 'ca', 'cb'],
            $values
        );
    }

    public function testCountIsNFactorialOverNMinusK(): void
    {
        // P(4, 2) = 4 * 3 = 12
        $values = iterator_to_array(Permutations::of(2, 'abcd')->toIterator(), false);
        self::assertCount(12, $values);
    }

    public function testFullPermutation(): void
    {
        // P(3, 3) = 3! = 6
        $values = iterator_to_array(Permutations::of(3, 'abc')->toIterator(), false);
        self::assertCount(6, $values);
        self::assertContains('abc', $values);
        self::assertContains('cba', $values);
    }

    public function testNoCharacterRepeatsWithinAValue(): void
    {
        $values = iterator_to_array(Permutations::of(2, 'abc')->toIterator(), false);

        foreach ($values as $value) {
            self::assertSame(strlen($value), count(array_unique(str_split($value))));
        }
    }

    public function testThrowsWhenLengthIsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Permutations::of(0, 'abc');
    }

    public function testThrowsWhenLengthIsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Permutations::of(-1, 'abc');
    }

    public function testThrowsWhenCharsetTooShort(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Permutations::of(3, 'ab'); // need at least 3 chars
    }

    public function testGeneratorIsReusable(): void
    {
        $perms = Permutations::of(2, 'abc');

        $first  = iterator_to_array($perms->toIterator(), false);
        $second = iterator_to_array($perms->toIterator(), false);

        self::assertSame($first, $second);
    }
}
