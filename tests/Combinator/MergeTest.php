<?php

declare(strict_types=1);

namespace Exakat\Generator\Tests\Combinator;

use Exakat\Generator\Booleans;
use Exakat\Generator\Digits;
use Exakat\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class MergeTest extends TestCase
{
    public function testMergesTwoGeneratorsEndToEnd(): void
    {
        $values = iterator_to_array(
            Letters::lower()->merge(Digits::all())->toIterator(),
            false
        );

        self::assertCount(36, $values); // 26 + 10
        self::assertSame('a', $values[0]);
        self::assertSame(0, $values[26]);
        self::assertSame(9, $values[35]);
    }

    public function testMergesThreeGenerators(): void
    {
        $values = iterator_to_array(
            Letters::lower()->merge(Digits::all(), Booleans::values())->toIterator(),
            false
        );

        self::assertCount(38, $values); // 26 + 10 + 2
    }

    public function testMixedTypesArePreserved(): void
    {
        $values = iterator_to_array(
            Letters::lower()->merge(Digits::all())->toIterator(),
            false
        );

        self::assertIsString($values[0]);
        self::assertIsInt($values[26]);
    }

    public function testMergedGeneratorIsReusable(): void
    {
        $merged = Letters::lower()->merge(Digits::all());

        $first  = iterator_to_array($merged->toIterator(), false);
        $second = iterator_to_array($merged->toIterator(), false);

        self::assertSame($first, $second);
    }
}
