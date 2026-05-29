# Ubiquitous Language — `exakat/generator`

> Canonical terminology for the systematic value generation library.  
> Prefer these terms in code, docs, issues, and conversation.

---

## 1. Core Concepts

| Term | Definition | Aliases to avoid |
|---|---|---|
| **Generator** | A library object that lazily yields values from a domain one at a time. | iterator, source, producer, stream |
| **Domain** | The complete, bounded set of values a Generator can yield (e.g. all lowercase letters). | set, space, universe, pool |
| **Sequence** | The ordered series of values produced when a Generator is iterated. | list, output, result, collection |
| **Laziness** | The property that values are computed only as they are consumed, never all at once. | deferred, on-demand, streaming |
| **Primitive** | A Generator whose domain is a single, atomic value type (e.g. letters, digits). | base, leaf, simple, raw |
| **Combinator** | An operation that composes one or more Generators into a new Generator. | operator, transformer, wrapper |

---

## 2. Primitive Generators

| Term | Definition | Aliases to avoid |
|---|---|---|
| **Letters** | The Primitive whose domain is individual alphabetic characters. | alphabet, chars, characters |
| **Digits** | The Primitive whose domain is integers within a bounded range. | numbers, integers, numerics |
| **Permutations** | The Primitive whose domain is ordered arrangements of distinct characters from a Charset; no character repeats within a single yielded value. | Strings, words, text, combinations |
| **Booleans** | The Primitive whose domain is `true`, `false`, and optionally `null`. | flags, truthiness |
| **Charset** | The explicit set of characters used as the alphabet for a Strings Generator. | character set, alphabet string, pool |
| **Range** | The pair of inclusive bounds (`$start`, `$end`) that defines a Digits domain. | interval, span, bounds |
| **Step** | The increment between successive values within a Range. | stride, increment, gap |

---

## 3. Combinators

| Term | Definition | Aliases to avoid |
|---|---|---|
| **Merge** | A Combinator that chains Generators end-to-end into a single Sequence. | union, concat, append, join |
| **Product** | A Combinator that yields all combinations of values across two or more Generators as tuples. | cartesian product, cross product, combinations |
| **Filter** | A Combinator that excludes values from a Sequence for which a Predicate returns false. | where, select, exclude |
| **Map** | A Combinator that applies a Transform to every value in a Sequence. | transform, convert, project |
| **Repeat** | A Combinator that yields all n-length sequences of values drawn from a single Generator (G^n). | power, permute, expand |

---

## 4. API & Composition

| Term | Definition | Aliases to avoid |
|---|---|---|
| **Factory Method** | A static constructor on a Primitive class that returns a ready-to-use Generator. | constructor, builder, make |
| **Fluent API** | The style of composing Generators by chaining Combinator calls directly on a Generator object. | method chaining, builder pattern |
| **Terminal Method** | The final call in a fluent chain that closes composition and returns a usable `\Iterator`. | finisher, materialiser, builder terminator |
| **toIterator()** | The sole Terminal Method in v1 — converts a composed Generator into a named `\Iterator` for assignment to a variable. | toArray(), get(), build(), iterate() |
| **Predicate** | A callable passed to Filter that returns `true` to keep a value, `false` to discard it. | callback, condition, test, rule |
| **Transform** | A callable passed to Map that takes a value and returns a new value. | callback, converter, mapper, formatter |
| **Tuple** | The array of values yielded by Product, one element per input Generator. | pair, combination, row |

---

## 5. Relationships

- A **Generator** yields a **Sequence** drawn from its **Domain**.
- A **Primitive** defines its **Domain** at construction time via a **Factory Method**.
- A **Combinator** takes one or more **Generators** as input and returns a new **Generator**.
- **Merge** preserves the individual **Domains**; **Product** creates a new composite **Domain** of **Tuples**.
- **Repeat(n)** is equivalent to **Product** applied n times on the same **Generator**.
- A **Charset** is the **Domain** of a **Permutations** Generator, not of a **Letters** Generator.
- **Permutations** and **Repeat** are complementary: **Permutations** forbids character repetition within a value; **Repeat** allows it.
- **Filter** and **Map** do not change the **Domain** definition — they narrow or reshape the **Sequence**.
- A fluent chain ends with exactly one **Terminal Method**; `->toIterator()` is the only Terminal Method in v1.
- A **Terminal Method** does not yield — it returns a `\Iterator` that can be stored in a variable and iterated later.

---

## 6. Example Dialogue

**Developer:** "I want to generate all possible pairs of a letter and a digit."  
**Domain expert:** "That's a **Product** of the **Letters** Generator and the **Digits** Generator — it yields **Tuples** like `['A', 3]`."

**Developer:** "Can I combine the lowercase and uppercase generators into one loop?"  
**Domain expert:** "Yes — use **Merge**. It chains two **Sequences** end-to-end. Don't say 'union'; that implies set deduplication, which Merge doesn't do."

**Developer:** "I need all 3-character strings over a-z."  
**Domain expert:** "Use **Repeat(3)** on a **Letters** Generator — it yields all 3-length **Tuples**. If you want joined strings, pipe it through **Map**."

**Developer:** "What if I only want even numbers?"  
**Domain expert:** "Apply a **Filter** with a **Predicate** — keep the **Digits** range as-is and let the Filter narrow the **Sequence**. Don't shrink the **Range** unless the boundary itself is the constraint."

**Developer:** "Do I have to put the whole chain inside the `foreach`?"  
**Domain expert:** "No — that's what **toIterator()** is for. It's the **Terminal Method**: it closes the fluent chain and gives you a `\Iterator` you can assign to a readable variable. The `foreach` then just consumes it."

---

## 7. Flagged Ambiguities

| Term | Problem | Resolution |
|---|---|---|
| `generator` (lowercase) | Overloaded: refers to both the PHP language feature (`yield`-based functions) and library objects. | Use **Generator** (capitalised) for library objects; say "PHP generator function" for the language feature. |
| `domain` | Collides with PHP namespace concept ("the `Exakat\Generator` domain"). | **Domain** always means a bounded set of values. Use "namespace" for PHP packaging. |
| `repeat` / `power` | Both appear in the spec for G^n behaviour. | Canonical term is **Repeat**. "Power" is acceptable in mathematical discussion only. |
| `combine` / `compose` / `chain` | All used loosely to describe connecting Generators. | Use **compose** when speaking abstractly; use the specific Combinator name (Merge, Product, etc.) in concrete discussion. |
| `charset` / `alphabet` | "Alphabet" is intuitive but collides with Letters' domain. | Use **Charset** exclusively for the string of characters passed to a Strings Generator. |
