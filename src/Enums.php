<?php

declare(strict_types=1);

namespace Dseguy\Generator;

final class Enums extends AbstractGenerator
{
    /** @var list<mixed> */
    private readonly array $items;

    /** @param list<mixed> $items */
    private function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function cases(string $enumClass): self
    {
        self::assertEnum($enumClass);

        return new self($enumClass::cases());
    }

    public static function values(string $enumClass): self
    {
        self::assertBackedEnum($enumClass);

        return new self(array_map(
            static fn (\BackedEnum $c): int|string => $c->value,
            $enumClass::cases(),
        ));
    }

    public static function names(string $enumClass): self
    {
        self::assertEnum($enumClass);

        return new self(array_map(
            static fn (\UnitEnum $c): string => $c->name,
            $enumClass::cases(),
        ));
    }

    public function getIterator(): \Generator
    {
        foreach ($this->items as $item) {
            yield $item;
        }
    }

    /** @phpstan-assert class-string<\UnitEnum> $enumClass */
    private static function assertEnum(string $enumClass): void
    {
        if (!enum_exists($enumClass)) {
            throw new \InvalidArgumentException("'{$enumClass}' is not a valid enum.");
        }
    }

    /** @phpstan-assert class-string<\BackedEnum> $enumClass */
    private static function assertBackedEnum(string $enumClass): void
    {
        self::assertEnum($enumClass);

        if (!is_a($enumClass, \BackedEnum::class, true)) {
            throw new \InvalidArgumentException("'{$enumClass}' is not a backed enum.");
        }
    }
}
