# Error Handling Evolution

## Before (≤ 0.0.1)
- Most invalid monadic operations threw **`RuntimeException`** with string messages.
    - Example: calling `unwrap()` on a `Left` or `Nothing` raised:
      ```php
      throw new RuntimeException("Cannot get value from Left");
      ```
    - Violations in bind/flatMap, ap, bracket, etc. were also reported as `RuntimeException`.

## After (0.1.0)
- Each invalid operation now throws a **domain-specific exception** under `PenguinPark\Monad\Exception`.
- Examples:

| Operation                                    | Old Behavior                                 | New Behavior (0.1.0)                                  |
|:---------------------------------------------|:---------------------------------------------|:------------------------------------------------------|
| `Left::unwrap()`                             | `RuntimeException("Cannot get value...")`    | `UnwrapLeft("Cannot get value from Left")`            |
| `Maybe::sequenceArray()` with wrong type     | `RuntimeException("Expected Maybe...")`      | `ContractViolation("...expects elements of type")`    |
| `IO::flatMap()` with non-IO return           | `RuntimeException("flatMap must return IO")` | `InvalidBindReturnType("IO::flatMap must return IO")` |
| `IO::ap()` with non-callable                 | `RuntimeException("ap expects callable")`    | `ApCallableExpected("...expects the receiver...")`    |
| `IO::bracket()` with wrong return type       | `RuntimeException("bracket must return IO")` | `BracketReturnTypeExpected("...must return IO")`      |
| `IO::unwrapNothing()`                        | `RuntimeException("Nothing to unwrap")`      | `UnwrapNothing("...")`                                |

### Benefits
- **Clearer semantics**: exception type signals exactly what went wrong.
- **Granular testing**: PHPUnit can assert on precise exception classes.
- **Extensibility**: future monads or combinators can introduce their own specialized exceptions.

## [0.1.1] - 2025-08-24
### Added
- **Validation** (applicative) under `PenguinPark\Monad\Validation`:
    - Classes: `Validation`, `Valid`, `Invalid`.
    - Semantics: accumulate errors (arrays) via `ap` using left-to-right merge; no `flatMap` by design.
    - API: `of`, `map`, `ap`, `fold`, `getOrElse`, `isValid`/`isInvalid`, `Validation::invalid($err)`.
    - Usage: prefer curried functions with `ap` (or `liftN` helpers when introduced).

### Tests
- Unit tests covering `Validation` mapping, folding, applicative combination, and error accumulation.
