# ADR 0001 — Infinite source detection via `isInfinite()` method, not PHP attribute

## Status

Accepted

## Context

When `Integers` was introduced as the first first-class Infinite Source, `product()` and `repeat()` needed a runtime mechanism to detect an infinite upstream and throw `\LogicException` immediately, rather than looping forever.

Two candidates were evaluated:

**PHP attribute** (`#[Infinite]` on the class declaration)

```php
#[\Attribute]
class Infinite {}

#[Infinite]
final class Integers extends AbstractGenerator { … }
```

Detection would require reflection: `(new \ReflectionClass($source))->getAttributes(Infinite::class) !== []`.

**`isInfinite(): bool` method on `GeneratorInterface`**

```php
// Integers
public function isInfinite(): bool { return true; }

// MapGenerator (delegates to source)
public function isInfinite(): bool { return $this->source->isInfinite(); }

// TakeGenerator (seals the chain)
public function isInfinite(): bool { return false; }
```

## Decision

Use `isInfinite(): bool` on `GeneratorInterface`, with `false` as the default in `AbstractGenerator`.

## Rationale

The attribute approach fails for chained generators. An attribute marks the **class declaration**, not the instance. When `Integers::natural()` is wrapped by `MapGenerator`, `FilterGenerator`, or `MergeGenerator`, those classes carry no `#[Infinite]` attribute. Reflection on them returns nothing, so `product()` would silently loop forever on:

```php
Integers::natural()->map(fn($n) => $n * 2)->product(Letters::lower())
```

The `isInfinite()` method propagates correctly through any chain depth:
- Safe combinators (`map`, `filter`, `merge`) delegate: `return $this->source->isInfinite()`
- `TakeGenerator` always returns `false`, sealing the chain
- `product()` and `repeat()` call `$this->isInfinite()` and throw if `true`

This matches the existing library philosophy: fail loudly at the call site, never silently produce wrong results.

## Consequences

- `isInfinite(): bool` is added to `GeneratorInterface` — a breaking change for any external implementation of the interface.
- All existing `AbstractGenerator` subclasses inherit the default `false` return with no changes required.
- `MapGenerator`, `FilterGenerator`, and `MergeGenerator` each gain a one-line `isInfinite()` override that delegates to their source.
- `TakeGenerator` returns `false` unconditionally, making it the sole combinator that converts an Infinite Source into a finite one.
