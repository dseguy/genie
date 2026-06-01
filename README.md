# genie — Systematic Value Generation for PHP

A PHP 8.2+ library for generating all values in a domain using lazy, composable iterators.

**Use case:** when code needs to walk an entire value domain — all letters, all digit pairs, all 3-character strings — this library provides the building blocks to express that concisely and memory-efficiently.

## Installation

```bash
composer require exakat/generator
```

## Requirements

- PHP 8.2+
- No runtime dependencies

---

## Quick start

```php
use Dseguy\Generator\Letters;
use Dseguy\Generator\Digits;

foreach (Letters::lower()->merge(Digits::all()) as $value) {
    // a, b, …, z, 0, 1, …, 9
}
```

---

## Primitive generators

Each primitive is a class with static factory methods. All generators are **lazy** — values are computed only when iterated.

### `Letters`

Yields individual alphabetic characters.

| Factory method      | Yields                           |
|---------------------|----------------------------------|
| `Letters::lower()`  | `a` … `z` (26 values)            |
| `Letters::upper()`  | `A` … `Z` (26 values)            |
| `Letters::all()`    | `a` … `z` then `A` … `Z` (52 values) |

```php
foreach (Letters::upper() as $c) {
    // A, B, C, …, Z
}
```

### `Digits`

Yields integers over a bounded range.

| Factory method | Yields |
|---|---|
| `Digits::all()` | `0` … `9` |
| `Digits::range(int $start, int $end, int $step = 1)` | integers from `$start` to `$end` inclusive, stepping by `$step` |

```php
foreach (Digits::range(1, 10, 2) as $n) {
    // 1, 3, 5, 7, 9
}
```

Throws `InvalidArgumentException` if `$start > $end` or `$step <= 0`.

### `Booleans`

Yields boolean (and optionally null) values.

| Factory method         | Yields                  |
|------------------------|-------------------------|
| `Booleans::values()`   | `true`, `false`         |
| `Booleans::withNull()` | `true`, `false`, `null` |

```php
foreach (Booleans::withNull() as $b) {
    // true, false, null
}
```

### `Permutations`

Yields all ordered arrangements of `$length` **distinct** characters drawn from a charset. No character repeats within a single value.

| Factory method | Yields |
|---|---|
| `Permutations::of(int $length, string $charset)` | All permutations of exactly `$length` distinct chars from `$charset` |

```php
foreach (Permutations::of(2, 'abc') as $s) {
    // 'ab', 'ac', 'ba', 'bc', 'ca', 'cb' — 6 values
}

// All 2-char permutations over the alphabet: 26×25 = 650 values
$codes = Permutations::of(2, 'abcdefghijklmnopqrstuvwxyz');
```

Throws `InvalidArgumentException` if `$length <= 0` or the charset has fewer than `$length` distinct characters.

---

## Combinators

Combinators are methods available on every generator. They return a new generator and preserve laziness. Chain them in any order.

### `->merge(GeneratorInterface ...$others)` — Union

Chains generators end-to-end into a single sequence.

```php
Letters::lower()->merge(Digits::all())
// yields: a, b, …, z, 0, 1, …, 9

Letters::lower()->merge(Letters::upper(), Digits::all())
// yields: a, …, z, A, …, Z, 0, …, 9
```

### `->product(GeneratorInterface ...$others)` — Cartesian product

Yields all combinations as flat arrays (one element per source generator).

```php
Letters::upper()->product(Digits::all())
// yields: ['A', 0], ['A', 1], …, ['Z', 9] — 260 tuples

Letters::lower()->product(Digits::all())->product(Booleans::values())
// yields: ['a', 0, true], ['a', 0, false], ['a', 1, true], …
```

Arrays from nested `product()` or `repeat()` calls are **flattened** — values spread into the result rather than nest.

### `->filter(callable $predicate)` — Conditional filter

Excludes values for which `$predicate` returns falsy.

```php
Digits::range(1, 100)->filter(fn($n) => $n % 2 === 0)
// yields: 2, 4, 6, …, 100

Letters::lower()->product(Digits::all())
    ->filter(fn($pair) => $pair[1] > 5)
// yields: ['a', 6], ['a', 7], …, ['z', 9]
```

### `->map(callable $transform)` — Transform

Applies a function to every yielded value.

```php
Letters::lower()->map(fn($c) => strtoupper($c))
// yields: A, B, …, Z

Letters::lower()->product(Digits::all())
    ->map(fn($pair) => implode('', $pair))
// yields: 'a0', 'a1', …, 'z9'
```

### `->repeat(int $n)` — Power (G^n)

Yields all `$n`-length sequences, with repetition allowed.

```php
Letters::lower()->repeat(2)
// yields: ['a','a'], ['a','b'], …, ['z','z'] — 676 tuples

Digits::all()->repeat(3)
// yields: [0,0,0], [0,0,1], …, [9,9,9] — 1000 tuples
```

Each yielded value is a flat array. Throws `InvalidArgumentException` if `$n <= 0`.

> **`repeat()` vs `Permutations`:** `repeat()` allows the same value to appear multiple times in one tuple; `Permutations` does not.

---

## Pragmatic usage examples

Real-world scenarios where systematic value generation is useful.

### Password / token brute-force testing

Generate all possible PIN codes or short alphanumeric tokens to verify a rate-limiter or lockout policy in tests.

```php
// All 4-digit PINs: 10^4 = 10 000 values
$pins = Digits::all()->repeat(4)
    ->map(fn($d) => implode('', $d));

foreach ($pins as $pin) {
    $response = $client->post('/login', ['pin' => $pin]);
    if ($response->status() === 429) {
        // lockout triggered — stop here
        break;
    }
}
```

```php
// All 6-character alphanumeric tokens (case-insensitive)
$tokens = Letters::lower()->merge(Digits::all())->repeat(6)
    ->map(fn($chars) => implode('', $chars));
```

### Exhaustive unit test data providers

Feed a PHPUnit data provider with every combination of inputs rather than hand-picking a few.

```php
// Test a validator against every (letter, digit) pair
public static function letterDigitPairs(): iterable
{
    return Letters::lower()->product(Digits::all())
        ->map(fn($pair) => [$pair[0], $pair[1]]);
}

/** @dataProvider letterDigitPairs */
public function test_accepts_alphanumeric(string $letter, int $digit): void
{
    $this->assertTrue(Validator::isAlphanumeric($letter . $digit));
}
```

```php
// Test a function against all boolean/null combinations
public static function truthyInputs(): iterable
{
    return Booleans::withNull()->map(fn($b) => [$b]);
}
```

### Generating test fixtures

Populate a database or in-memory store with a systematic set of records.

```php
// One user per letter of the alphabet
foreach (Letters::lower() as $initial) {
    User::factory()->create(['username' => "user_{$initial}"]);
}
```

```php
// All combinations of role × status for permission matrix tests
$roles   = ['admin', 'editor', 'viewer'];
$statuses = ['active', 'suspended', 'pending'];

// Build a generator from an arbitrary list using merge + map
$roleGen   = Letters::lower()->filter(fn($c) => in_array($c, ['a','e','v']))->map(fn($c) => match($c) {
    'a' => 'admin', 'e' => 'editor', default => 'viewer'
});
```

A simpler pattern when the domain is small — iterate the product of two PHP arrays:

```php
foreach (Digits::range(0, count($roles) - 1)->product(Digits::range(0, count($statuses) - 1)) as [$ri, $si]) {
    $role   = $roles[$ri];
    $status = $statuses[$si];
    // test every role × status combination
}
```

### Slug / identifier collision detection

Verify that a slug generator produces unique output across all inputs.

```php
$seen = [];
foreach (Letters::lower()->repeat(3)->map(fn($t) => implode('', $t)) as $trigram) {
    $slug = MySlugifier::slugify($trigram);
    assert(!isset($seen[$slug]), "Collision on: $trigram → $slug");
    $seen[$slug] = true;
}
```

### Scanning configuration ranges

Walk every valid port in a range, every HTTP status code, or every timeout value to verify system behaviour.

```php
// Check that all privileged ports are refused
$privileged = Digits::range(1, 1023)->filter(fn($p) => !in_array($p, $allowList));
foreach ($privileged as $port) {
    $this->assertFalse($server->canBind($port));
}
```

```php
// Verify all 5xx codes are handled
$serverErrors = Digits::range(500, 599);
foreach ($serverErrors as $code) {
    $this->assertInstanceOf(ServerException::class, $handler->handle($code));
}
```

### Character set validation

Probe a sanitiser or encoder against every character in a defined alphabet.

```php
// Every character that must survive HTML encoding unchanged
$safe = Letters::all()->merge(Digits::all());
foreach ($safe as $char) {
    $this->assertSame($char, htmlspecialchars($char));
}
```

```php
// Detect which characters a legacy system rejects
$rejected = [];
foreach (Letters::all()->merge(Digits::all()) as $char) {
    if (!$legacySystem->accepts($char)) {
        $rejected[] = $char;
    }
}
```

### Generating SQL / query permutations

Build every variant of a parameterised query for fuzz-style integration tests.

```php
// All ORDER BY direction × LIMIT combinations
$directions = ['ASC', 'DESC'];
$limits     = Digits::range(1, 5);   // 1 … 5

foreach ($limits as $limit) {
    foreach ($directions as $dir) {
        $results = $db->query("SELECT * FROM items ORDER BY name $dir LIMIT $limit");
        $this->assertCount($limit, $results);
    }
}
```

---

## Terminal method

After composing a chain, call `->toIterator()` to obtain a plain `\Iterator` that can be assigned to a variable and passed around.

```php
$candidates = Letters::lower()->merge(Digits::all())->toIterator();

foreach ($candidates as $value) {
    // a, b, …, z, 0, 1, …, 9
}
```

All generators also implement `\IteratorAggregate`, so they can be used directly in `foreach` without calling `->toIterator()`.

---

## Composing chains

Combinators compose freely. Each step returns a new generator.

```php
use Dseguy\Generator\Letters;
use Dseguy\Generator\Digits;
use Dseguy\Generator\Booleans;
use Dseguy\Generator\Permutations;

// All lowercase alphanumeric characters
$alphanumeric = Letters::lower()->merge(Digits::all());

// All uppercase letter + single digit pairs
$pairs = Letters::upper()->product(Digits::all());
foreach ($pairs as [$letter, $digit]) { … }

// All 3-letter lowercase trigrams (26^3 = 17 576 values)
$trigrams = Letters::lower()->repeat(3);
foreach ($trigrams as [$a, $b, $c]) { … }

// Even numbers from 1 to 50
$evens = Digits::range(1, 50)->filter(fn($n) => $n % 2 === 0);

// Letters mapped to their ASCII code
$ascii = Letters::all()->map(fn($c) => ord($c));

// All 2-char permutations over a-z, formatted as strings
$codes = Permutations::of(2, 'abcdefghijklmnopqrstuvwxyz')
    ->map(fn($s) => strtoupper($s));
```

---

## API reference

### Namespace

```php
use Dseguy\Generator\Letters;
use Dseguy\Generator\Digits;
use Dseguy\Generator\Booleans;
use Dseguy\Generator\Permutations;
```

### `GeneratorInterface`

All generators implement `GeneratorInterface`, which extends `\IteratorAggregate`.

| Method | Returns | Description |
|--------|---------|-------------|
| `getIterator()` | `\Generator` | Yields domain values |
| `merge(...$others)` | `GeneratorInterface` | Chain generators end-to-end |
| `product(...$others)` | `GeneratorInterface` | Cartesian product |
| `filter($predicate)` | `GeneratorInterface` | Filter by predicate |
| `map($transform)` | `GeneratorInterface` | Transform each value |
| `repeat(int $n)` | `GeneratorInterface` | All n-length sequences |
| `toIterator()` | `\Iterator` | Materialise the chain |

---

## Design principles

- **Lazy by default** — every generator uses PHP `yield`; no value is computed until iterated.
- **Composable** — any generator can be used as input to any combinator.
- **Fluent API** — combinators chain directly on generator objects.
- **Deterministic** — fixed iteration order; generators can be re-iterated to produce the same sequence.
- **Fail fast** — invalid constructor arguments throw `InvalidArgumentException` immediately, never produce silent empty sequences.
- **No runtime dependencies** — pure PHP; only dev dependencies (PHPUnit, PHPStan).

---

## Out of scope (v1)

- Random / shuffled generation
- Weighted generators
- CSV / file-based value sources
- Output adapters (`->toArray()`, `->toCsv()`, `->echo()`)
- Framework integrations (Laravel, Symfony)
