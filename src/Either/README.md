# Either (L | R)

Disjoint union type representing a value of one of two possible types (a sum type).
By convention, Left is used for the error/alternative path, and Right for the success path.
Either in this library is right-biased: map/flatMap/ap operate on Right.

## Classes
- [Either&lt;L,R&gt;](../../docs/Either/Either.md) - abstract base with right-biased operations
- [Left&lt;L,R&gt;](../../docs/Either/Left.md) - concrete Left branch (typically error)
- [Right&lt;L,R&gt;](../../docs/Either/Right.md) - concrete Right branch (typically success)

## Key capabilities
- Functor: map on Right
- Monad: flatMap chains Right computations, Left short-circuits
- Applicative: ap, pure, liftA2
- Bifunctor-ish: mapLeft, bimap
- Interop: getRight/getLeft as Maybe, conversions to/from Maybe/IO where appropriate in those modules

## Interoperability with other monads
- Maybe: Either::getRight() returns Maybe::just on Right and ::nothing on Left; getLeft() similarly for Left. Maybe::toEither(&dollar;ifNothing) converts Maybe to Either.
- IO: IO::fromEither converts Left to a throwing IO and Right to a pure IO. Either::tryCatch/try wraps exceptions as Left or values as Right.
- ListM: ListM::traverseEither/sequenceEither can accumulate or short-circuit on first Left while mapping over lists.
- Reader/State/Writer: Either composes well inside traversals/lifts; e.g., use Either in ListM traversals or lift Either results into IO/State workflows for error-aware flows.

## Examples
1) Parsing with error handling
```php
use PenguinPark\Monad\Either\Either;

$parseInt = fn(string $s) => is_numeric($s)
    ? Either::right((int)$s)
    : Either::left('NaN');

$result = $parseInt('10')
    ->map(fn($n) => $n + 5)   // Right(15)
    ->flatMap(fn($n) => $n > 10 ? Either::right($n) : Either::left('too small'));
```

2) Converting to Maybe
```php
$maybeRight = $result->getRight(); // Just(value) if Right, Nothing if Left
$maybeLeft  = $result->getLeft();  // Just(error) if Left, Nothing if Right
```

3) Using with IO
```php
use PenguinPark\Monad\IO\IO;

$io = IO::fromEither($result); // Left => throws on run, Right => pure value
$value = $io->unsafeRun();
```
