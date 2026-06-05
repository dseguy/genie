<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Digits;
use Dseguy\Generator\FromCallable;
use PHPUnit\Framework\TestCase;

final class FromCallableTest extends TestCase
{
    public function testOfYieldsAllValuesFromGenerator(): void
    {
        $gen = FromCallable::of(static function (): \Generator {
            yield 'a';
            yield 'b';
            yield 'c';
        });

        $values = iterator_to_array($gen->toIterator(), false);
        self::assertSame(['a', 'b', 'c'], $values);
    }

    public function testOfYieldsAllValuesFromIterator(): void
    {
        $gen = FromCallable::of(static fn (): \ArrayIterator => new \ArrayIterator([10, 20, 30]));

        $values = iterator_to_array($gen->toIterator(), false);
        self::assertSame([10, 20, 30], $values);
    }

    public function testKeysAreDropped(): void
    {
        $gen = FromCallable::of(static function (): \Generator {
            yield 'x' => 1;
            yield 'y' => 2;
            yield 'z' => 3;
        });

        $values = iterator_to_array($gen->toIterator(), false);
        self::assertSame([1, 2, 3], $values);
    }

    public function testEmptyTraversableYieldsNothing(): void
    {
        $gen = FromCallable::of(static fn (): \ArrayIterator => new \ArrayIterator([]));

        $values = iterator_to_array($gen->toIterator(), false);
        self::assertSame([], $values);
    }

    public function testGeneratorIsReusable(): void
    {
        $gen = FromCallable::of(static function (): \Generator {
            yield 1;
            yield 2;
            yield 3;
        });

        $first  = iterator_to_array($gen->toIterator(), false);
        $second = iterator_to_array($gen->toIterator(), false);

        self::assertSame($first, $second);
    }

    public function testComposesWithProduct(): void
    {
        $source = FromCallable::of(static function (): \Generator {
            yield 'a';
            yield 'b';
        });

        $values = iterator_to_array($source->product(Digits::range(1, 2))->toIterator(), false);
        self::assertSame([['a', 1], ['a', 2], ['b', 1], ['b', 2]], $values);
    }

    public function testFactoryIsCalledFreshEachIteration(): void
    {
        $callCount = 0;

        $gen = FromCallable::of(static function () use (&$callCount): \Generator {
            ++$callCount;
            yield 42;
        });

        iterator_to_array($gen->toIterator(), false);
        iterator_to_array($gen->toIterator(), false);

        self::assertSame(2, $callCount);
    }
}
