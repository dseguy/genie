<?php

declare(strict_types=1);

namespace Exakat\Generator\Tests\Combinator;

use Exakat\Generator\Booleans;
use Exakat\Generator\Digits;
use Exakat\Generator\Letters;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function testProductOfTwoGenerators(): void
    {
        $values = iterator_to_array(
            Letters::upper()->product(Digits::all())->toIterator(),
            false
        );

        self::assertCount(260, $values); // 26 × 10
        self::assertSame(['A', 0], $values[0]);
        self::assertSame(['A', 9], $values[9]);
        self::assertSame(['B', 0], $values[10]);
        self::assertSame(['Z', 9], $values[259]);
    }

    public function testTuplesAreFlat(): void
    {
        $values = iterator_to_array(
            Letters::lower()->product(Digits::all())->toIterator(),
            false
        );

        foreach ($values as $tuple) {
            self::assertIsArray($tuple);
            self::assertCount(2, $tuple);
        }
    }

    public function testThreeWayProductYieldsFlatTuples(): void
    {
        $values = iterator_to_array(
            Letters::lower()->product(Digits::all(), Booleans::values())->toIterator(),
            false
        );

        self::assertCount(520, $values); // 26 × 10 × 2

        foreach ($values as $tuple) {
            self::assertIsArray($tuple);
            self::assertCount(3, $tuple); // flat — not [['a', 0], true]
        }

        self::assertSame(['a', 0, true], $values[0]);
        self::assertSame(['a', 0, false], $values[1]);
    }

    public function testProductGeneratorIsReusable(): void
    {
        $product = Letters::lower()->product(Digits::all());

        $first  = iterator_to_array($product->toIterator(), false);
        $second = iterator_to_array($product->toIterator(), false);

        self::assertSame($first, $second);
    }
}
