# Specification — `exakat/generator`

## Overview

A PHP 8.2+ library for **systematic value generation**.  
Provides lazy iterators (PHP generators) over common value domains, composable via a **fluent chainable API**.

The driving use case is **general-purpose iteration**: when code needs to walk an entire domain of values (e.g. all letters, all digit pairs, all 3-character strings), this library supplies the building blocks to express that concisely and memory-efficiently.

---

## Package metadata

| Key              | Value                  |
|------------------|------------------------|
| Packagist name   | `exakat/generator`     |
| PHP requirement  | `^8.2`                 |
| Namespace        | `Exakat\Generator`     |

---

## Design principles

- **Lazy by default** — every generator uses PHP's `yield`; no value is computed until iterated.
- **Fluent API** — combinators are chained directly on generator objects.
- **Composable** — any generator can be used as input to any combinator.
- **No dependencies** — pure PHP, nothing external required at runtime.

---

## Primitive generators

Each primitive is a class with named constructors (static factory methods).

### `Letters`

Generates individual characters.

| Factory method      | Yields              |
|---------------------|---------------------|
| `Letters::lower()`  | `a` … `z`           |
| `Letters::upper()`  | `A` … `Z`           |
| `Letters::all()`    | `a` … `z`, then `A` … `Z` — equivalent to `Letters::lower()->merge(Letters::upper())` |

### `Digits`

Generates integers over a range.

| Factory method                  | Yields                         |
|---------------------------------|--------------------------------|
| `Digits::range(int $start, int $end, int $step = 1)` | integers from `$start` to `$end` (inclusive), stepping by `$step` |
| `Digits::all()`                 | `0` … `9`                      |

### `Permutations`

Generates all ordered arrangements of `$length` distinct characters drawn from a Charset. No character repeats within a single yielded value. This is a permutation generator — distinct from `->repeat()`, which allows repetition.

| Factory method                                          | Yields                                                             |
|---------------------------------------------------------|--------------------------------------------------------------------|
| `Permutations::of(int $length, string $charset)`        | All permutations of exactly `$length` distinct chars from `$charset` |

> `$charset` is a plain string; each character in it is one element of the alphabet. Characters do not repeat within a single yielded value. `Permutations::of(2, 'abc')` yields `'ab'`, `'ac'`, `'ba'`, `'bc'`, `'ca'`, `'cb'`.

### `Booleans`

Generates boolean (and optionally null) values.

| Factory method         | Yields                  |
|------------------------|-------------------------|
| `Booleans::values()`   | `true`, `false`         |
| `Booleans::withNull()` | `true`, `false`, `null` |

---

## Terminal method

After composing a Generator via the fluent API, call `->toIterator()` to obtain a plain PHP `\Iterator` that can be assigned to a variable and passed around before iteration.

### `->toIterator(): \Iterator`

**Materialises** the composed Generator into a named, reusable iterator — the idiomatic way to end a fluent chain.

```php
// Readable: expression is named before the loop
$candidates = Letters::lower()->merge(Digits::all())->toIterator();
foreach ($candidates as $value) {
    // a, b, …, z, 0, 1, …, 9
}

// Contrast with the inline form (still valid, but less readable):
foreach (Letters::lower()->merge(Digits::all()) as $value) { … }
```

> `->toIterator()` is the **only** terminal method in v1. All other output forms (arrays, CSV, database, text, echo) are deferred to later versions.

---

## Combinators

Combinators are methods available on every generator object. They return a new generator, preserving laziness.

### `->merge(GeneratorInterface ...$others)`

**Union** — chains generators end-to-end into a single sequence.

```php
Letters::lower()->merge(Digits::all())
// yields: a, b, …, z, 0, 1, …, 9
```

### `->product(GeneratorInterface ...$others)`

**Cartesian product** — yields all combinations as arrays.

```php
Letters::upper()->product(Digits::all())
// yields: ['A', 0], ['A', 1], …, ['Z', 9]
```

### `->filter(callable $predicate)`

**Filter** — excludes values for which `$predicate` returns `false`.

```php
Digits::range(1, 100)->filter(fn($n) => $n % 2 === 0)
// yields: 2, 4, 6, …, 100
```

### `->map(callable $transform)`

**Map** — transforms each yielded value.

```php
Letters::lower()->map(fn($c) => strtoupper($c))
// yields: A, B, …, Z
```

### `->repeat(int $n)`

**Power** — yields all `$n`-length sequences from the generator (G^n).

```php
Letters::lower()->repeat(2)
// yields: ['a','a'], ['a','b'], …, ['z','z']
```

---

## API examples

```php
use Exakat\Generator\Letters;
use Exakat\Generator\Digits;
use Exakat\Generator\Booleans;
use Exakat\Generator\Permutations;

// All lowercase letters merged with digits — named variable via toIterator()
$alphanumeric = Letters::lower()->merge(Digits::all())->toIterator();
foreach ($alphanumeric as $value) {
    // a, b, …, z, 0, 1, …, 9
}

// All uppercase letter + digit pairs
$pairs = Letters::upper()->product(Digits::all())->toIterator();
foreach ($pairs as [$letter, $digit]) {
    // ['A', 0], ['A', 1], …
}

// All 3-letter lowercase trigrams
$trigrams = Letters::lower()->repeat(3)->toIterator();
foreach ($trigrams as $trigram) {
    // ['a','a','a'], ['a','a','b'], …
}

// Even numbers from 1 to 50
$evens = Digits::range(1, 50)->filter(fn($n) => $n % 2 === 0)->toIterator();
foreach ($evens as $n) {
    // 2, 4, …, 50
}

// All 2-char permutations over a-z (no repeated characters per value)
$codes = Permutations::of(2, 'abcdefghijklmnopqrstuvwxyz')->toIterator();
foreach ($codes as $s) {
    // 'ab', 'ac', …, 'zy' — 650 values (26×25)
}

// Boolean edge cases
$bools = Booleans::withNull()->toIterator();
foreach ($bools as $b) {
    // true, false, null
}
```

---

## Quality

### PHPUnit

- Unit tests for every primitive generator.
- Unit tests for every combinator.
- Tests verify laziness (generator is not exhausted before iteration).
- Edge cases: empty ranges, single-element sets, `repeat(1)`, nested products.

### PHPStan

- Static analysis at **level 8**.
- Strict return types on all public methods.
- Generator return types use `\Generator` with full template annotations where applicable.

---

## Reserved for future versions (RFU)

- **Generation order** — configurable ordering of Primitive output (ascending, descending, interleaved, shuffled). All Primitives use a single fixed natural order in v1.

---

## Out of scope (v1)

- Random / shuffled generation (non-deterministic)
- Weighted generators
- CSV / file-based value sources
- Framework integrations (Laravel, Symfony)
- Output adapters: `->toArray()`, `->toCsv()`, `->toText()`, `->echo()`, database writers — deferred to later versions.
