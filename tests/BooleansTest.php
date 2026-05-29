<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Booleans;
use PHPUnit\Framework\TestCase;

final class BooleansTest extends TestCase
{
    public function testValuesYieldsTrueAndFalse(): void
    {
        $values = iterator_to_array(Booleans::values()->toIterator(), false);
        self::assertSame([true, false], $values);
    }

    public function testWithNullYieldsTrueFalseNull(): void
    {
        $values = iterator_to_array(Booleans::withNull()->toIterator(), false);
        self::assertSame([true, false, null], $values);
    }

    public function testGeneratorIsReusable(): void
    {
        $bools = Booleans::withNull();

        $first  = iterator_to_array($bools->toIterator(), false);
        $second = iterator_to_array($bools->toIterator(), false);

        self::assertSame($first, $second);
    }
}
