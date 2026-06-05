<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testOfYieldsAllItems(): void
    {
        $values = iterator_to_array(Collection::of([1, 'a', true, null])->toIterator(), false);
        self::assertSame([1, 'a', true, null], $values);
    }

    public function testOfWithEmptyArrayYieldsNothing(): void
    {
        $values = iterator_to_array(Collection::of([])->toIterator(), false);
        self::assertSame([], $values);
    }

    public function testOfNormalizesKeys(): void
    {
        $values = iterator_to_array(Collection::of(['x' => 1, 'y' => 2])->toIterator(), false);
        self::assertSame([1, 2], $values);
    }

    public function testGeneratorIsReusable(): void
    {
        $gen = Collection::of([10, 20, 30]);

        $first  = iterator_to_array($gen->toIterator(), false);
        $second = iterator_to_array($gen->toIterator(), false);

        self::assertSame($first, $second);
    }
}
