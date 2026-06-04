<?php

declare(strict_types=1);

namespace Dseguy\Generator\Tests;

use Dseguy\Generator\Enums;
use PHPUnit\Framework\TestCase;

enum Suit { case Hearts; case Diamonds; case Clubs; case Spades; }
enum Priority: int { case Low = 1; case Medium = 2; case High = 3; }
enum Color: string { case Red = 'red'; case Green = 'green'; case Blue = 'blue'; }

final class EnumsTest extends TestCase
{
    public function testCasesYieldsAllPureEnumCases(): void
    {
        $values = iterator_to_array(Enums::cases(Suit::class)->toIterator(), false);
        self::assertSame([Suit::Hearts, Suit::Diamonds, Suit::Clubs, Suit::Spades], $values);
    }

    public function testCasesYieldsAllBackedEnumCases(): void
    {
        $values = iterator_to_array(Enums::cases(Priority::class)->toIterator(), false);
        self::assertSame([Priority::Low, Priority::Medium, Priority::High], $values);
    }

    public function testValuesYieldsIntBackingValues(): void
    {
        $values = iterator_to_array(Enums::values(Priority::class)->toIterator(), false);
        self::assertSame([1, 2, 3], $values);
    }

    public function testValuesYieldsStringBackingValues(): void
    {
        $values = iterator_to_array(Enums::values(Color::class)->toIterator(), false);
        self::assertSame(['red', 'green', 'blue'], $values);
    }

    public function testNamesYieldsCaseNamesForPureEnum(): void
    {
        $values = iterator_to_array(Enums::names(Suit::class)->toIterator(), false);
        self::assertSame(['Hearts', 'Diamonds', 'Clubs', 'Spades'], $values);
    }

    public function testNamesYieldsCaseNamesForBackedEnum(): void
    {
        $values = iterator_to_array(Enums::names(Color::class)->toIterator(), false);
        self::assertSame(['Red', 'Green', 'Blue'], $values);
    }

    public function testGeneratorIsReusable(): void
    {
        $gen = Enums::cases(Suit::class);

        $first  = iterator_to_array($gen->toIterator(), false);
        $second = iterator_to_array($gen->toIterator(), false);

        self::assertSame($first, $second);
    }

    public function testCasesThrowsForNonEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Enums::cases(\stdClass::class);
    }

    public function testNamesThrowsForNonEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Enums::names(\stdClass::class);
    }

    public function testValuesThrowsForPureEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Enums::values(Suit::class);
    }

    public function testValuesThrowsForNonEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Enums::values(\stdClass::class);
    }
}
