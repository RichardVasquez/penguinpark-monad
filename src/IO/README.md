# IO — Lazy, referentially transparent effects

IO&lt;A&gt; represents a delayed computation that can be executed with unsafeRun()/get().
It supports transformations, sequencing, error handling, and Applicative operations.

## Classes
- [IO&lt;A&gt;](../../docs/IO) - single class encapsulating a thunk that yields A when executed

## Key capabilities
- Laziness: describe effects now, run later
- Functor/Monad/Applicative: map, flatMap, ap, pure, liftA2
- Error channel: attempt() to Either, mapError/handleError/handleErrorWith for recovery
- Resource-safety: bracket(acquire, use, release)
- Memoization: memoize to cache result for repeat runs

## Interoperability with other monads
- Either: IO::fromEither(Either) converts Right to pure IO and Left to a failing IO (throws on run). IO::attempt() returns IO&lt;Either&lt;Throwable,A&gt;&gt;.
- Maybe: IO::fromMaybe(Maybe, &dollar;ifNothing) converts Just to pure IO and Nothing to a failing IO with provided Throwable or message.
- ListM: Use liftA2/ap/map to combine multiple IOs derived from a list; or map a list to IO and then sequence with custom utilities outside the library if needed.
- Reader/State/Writer: Often used at boundaries. You can produce IO from the results of these monads or embed their outputs into IO computations.

## Examples
1) Basic mapping & running
```php
use PenguinPark\Monad\IO\IO;

$read = IO::delay(fn() => trim("  hello  "));
$upper = $read->map(fn($s) => strtoupper($s));

echo $upper->unsafeRun(); // HELLO
```

2) Error handling to Either
```php
$danger = IO::delay(function () { throw new \RuntimeException('boom'); });
$captured = $danger->attempt()->unsafeRun(); // Either<Throwable,string>

if ($captured->isLeft()) {
    // handle error stored inside Left
}
```

3) Using bracket
```php
$acquire = IO::delay(fn() => fopen('file.txt', 'r'));
$use = fn($h) => IO::delay(fn() => fgets($h));
$release = fn($h) => IO::delay(fn() => fclose($h));

$line = IO::bracket($acquire, $use, $release)->unsafeRun();
```

4) From Either / Maybe
```php
use PenguinPark\Monad\Either\Either;
use PenguinPark\Monad\Maybe\Maybe;

$ioFromEither = IO::fromEither(Either::right(42));
$ioFromMaybe  = IO::fromMaybe(Maybe::just('ok'), 'Nothing error');

[$a, $b] = [$ioFromEither->unsafeRun(), $ioFromMaybe->unsafeRun()];
```
