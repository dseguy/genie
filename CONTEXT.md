# Context — `dseguy/genie`

> Domain glossary. No implementation details. One sentence per term.

---

| Term | Definition |
|---|---|
| **Generator** | A reusable object that produces a lazy Sequence from a Domain; it can be iterated multiple times, each time yielding a fresh Sequence from the start. |
| **GeneratorInterface** | The contract all Generators implement; extends `\IteratorAggregate` so that each iteration produces a fresh PHP generator function internally. |
| **AbstractGenerator** | The base class all Primitives and Combinators extend; provides the five combinator methods and `toIterator()` so no logic is duplicated. |
| **Tuple** | A flat array of values yielded by Product or Repeat; always one level deep regardless of how many Generators were composed. |
| **Mixed Sequence** | A Sequence produced by Merge whose values may span multiple types; type homogeneity is the caller's responsibility, not enforced by the library. |
| **Invalid Input** | Any argument that makes a Generator's Domain undefined (e.g. reversed Range, zero Repeat, zero-length Permutation); always throws `\InvalidArgumentException` at construction, never silently yields an empty Sequence. |
| **Enums** | The Primitive that yields values derived from a PHP 8.1+ enum; three factory methods expose different projections — `cases()` for the case objects, `names()` for their string names, and `values()` for the backing values of backed enums. |
| **Collection** | The Primitive that wraps an arbitrary PHP array as a Generator source; array keys are discarded and only values are yielded, re-indexed from zero for consistency with all other Primitives. |
| **FromCallable** | The Primitive that wraps a factory callable returning a `\Traversable`; the factory is invoked fresh on every iteration so the source is fully reusable and composable — including with Product and Repeat — unlike a raw PHP `\Generator` instance, which is single-use. |
| **Factory** | A callable with signature `(): \Traversable` passed to `FromCallable::of()`; it is the caller's responsibility to ensure the factory is pure (no observable side effects per call) and finite when used with combinators that require full traversal. |
| **Permutations** | The Primitive that yields all ordered arrangements of `$length` distinct characters drawn from a Charset, with no character repeating within a single value; distinct from Repeat, which allows repetition. |
| **Generation Order** | The order in which a Primitive yields its values (e.g. ascending, descending, interleaved); fixed to a single natural default in v1 — `Letters::all()` yields lowercase then uppercase, equivalent to `Letters::lower()->merge(Letters::upper())`. Configurable ordering is reserved for a future version (RFU). |
