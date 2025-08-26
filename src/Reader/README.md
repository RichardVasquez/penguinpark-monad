# Reader — Dependency injection via environment

Reader&lt;E, A&gt; represents a computation that depends on a shared environment E and produces a value A.
It wraps a function E -&gt; A and provides functor/monad/applicative utilities.

## Classes
- [Reader&lt;E, A&gt;](../../docs/Reader/Reader.md) - single class wrapping a function from E to A

## Key capabilities
- Construction: reader(fn E-&gt;A), asks(fn E-&gt;B), ask() (identity environment), of/pure (ignore env)
- Transformations: map, flatMap, local (transform environment for a sub-computation)
- Applicative: ap, liftA2
- Running: run(&dollar;env)

## Interoperability with other monads
- Maybe/Either: Often used to validate or construct context-dependent values; the produced values can be Maybe/Either, or you can map/flatMap to produce them.
- IO: Reader can build IO programs from environment-bound services (e.g., DB connections). You can run Reader with an env to obtain IO and then unsafeRun.
- ListM: Produce lists from env or traverse a list producing Readers, then combine with liftA2/ap.
- State/Writer: Less common but you can nest: produce State/Writer computations parameterized by env or carry env-derived logging.

## Examples
1) Basic usage and local
```php
use PenguinPark\Monad\Reader\Reader;

$type = Reader::asks(fn(array $env) => $env['type'] ?? 'guest');
$greet = $type->map(fn($t) => ($t === 'admin') ? 'Welcome back' : 'Hello');

echo $greet->run(['type' => 'admin']); // Welcome back

// Change the environment shape for a portion
$greetFromUser = $greet->local(fn($user) => ['type' => $user['role']]);
echo $greetFromUser->run(['role' => 'guest']); // Hello
```

2) Building IO from Reader
```php
use PenguinPark\Monad\IO\IO;

$fetchLine = Reader::asks(fn(array $env) => IO::delay(fn() => $env['source']()));
$program   = $fetchLine->map(fn(IO $io) => $io->map('trim'));

$value = $program->run(['source' => fn() => "  data  "])->unsafeRun(); // 'data'
```
