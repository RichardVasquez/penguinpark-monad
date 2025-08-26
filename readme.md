# PenguinPark/Monad

> A small pile of PHP monads (and friends) that makes functional-style code not terrible.

## Why this exists (a tiny tale of regret)

I made a mistake.

I decided to make a parser, and I’d gotten very used to what the **Sprache** library does for .NET. Underneath it is a bunch of LINQ magic — which PHP does not have. So: a couple of years of poking at **ANTLR**, reading about **packrat parsers**, trying to remember the difference between **LL** and **LR** (no need to explain it to me), and getting **really, really peeved**.

Then I ran across **OCaml**, and all these friendly tutorials showing how easy and *pleasant* combinator parsers are. Great! Still not PHP. *sigh*

The “obvious solution” was to write a parser in C and bind it into PHP.
No. Wait. Bad idea.

So I looked at everything I’d picked up over the years… **we hates it, we does; it burns, precious!**

The need didn’t go away: I still want a combinator parser. My first drafts were drowning in edge cases and brittle failure paths.

**Start over.** Give the parser a foundation. This library is that foundation. *Tada!*

It’s licensed **Apache-2.0** — please do whatever you want with it. Send PRs; I know I’m not covering everything (yet) in functions or tests. I’m calling this “good enough for now.” Enjoy!

---

## Features

- The usual suspects:
    - `Maybe` – optional values without `null` sadness
    - `Either` – success (`Right`) or error (`Left`) with typed error channels
    - `IO` – wrap side effects (`delay`, `mapError`, `handleErrorWith`, `bracket`)
    - `Reader` – thread read-only context (config, services) through pure code
    - `State` – state threading: `S -> (S, A)`
    - `Writer` – accumulate logs alongside values
    - `ListM` – list/sequence monad (cartesian/branching)
- Clear exceptions (0.1.0+): domain-specific error types instead of generic runtime errors
- Helpful docs under `docs/`
- PHPUnit + Infection setup (mutation testing)

> **Coming from other FP ecosystems?**  
> See [`docs/aliases.md`](docs/aliases.md) for a Rosetta Stone of names (Haskell, Cats, Arrow, Fantasy Land, Elm, Rust, Swift, F#).

---

## Install

Requires **PHP 8.4+**

```bash
composer require penguinpark/monad
````

Autoloads under `PenguinPark\Monad\`.

---

## Quick taste

### Maybe

```php
use PenguinPark\Monad\Maybe\Maybe;

$answer = Maybe::of(41)
    ->map(fn (int $x) => $x + 1)
    ->getOrElse(0); // 42
```

### Either

```php
use PenguinPark\Monad\Either\Either;
use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;

$parseInt = function(string $s): Either {
    return ctype_digit($s) ? new Right((int)$s) : new Left('bad int');
};

$result = $parseInt('123')
    ->map(fn (int $n) => $n + 1)                 // Right(124)
    ->getOrElse(0);                              // 124

$error  = $parseInt('nope')->getOrElse(0);       // 0
```

### IO

```php
use PenguinPark\Monad\IO\IO;
use InvalidArgumentException;

// Lazy effect
$io = IO::delay(fn () => file_get_contents('/etc/hosts'))
    ->map('strlen')
    ->mapError(fn (\Throwable $e) => new InvalidArgumentException('read failed', previous: $e));

$len = $io->unsafeRun(); // int
```

Resource-safety:

```php
use PenguinPark\Monad\IO\IO;

$readFirstLine = IO::bracket(
    acquire: fn () => fopen('/etc/hosts', 'r'),
    use:     fn ($fh)  => IO::delay(fn () => fgets($fh)),
    release: fn ($fh)  => IO::delay(fn () => fclose($fh))
);

$line = $readFirstLine->unsafeRun();
```

### Writer

```php
use PenguinPark\Monad\Writer\Writer;

[$log, $val] = Writer::tell('start')
    ->map(fn () => 'value')
    ->tell('done')
    ->run();        // $log = ['start','done'], $val = 'value'
```

---

## Error model (custom exceptions)

Common failure modes throw specific exceptions under `PenguinPark\Monad\Exception`:

* `UnwrapNothing`, `UnwrapLeft`
* `ContractViolation` (shape/type contracts)
* `InvalidBindReturnType`, `ApCallableExpected`, `BracketReturnTypeExpected`

This lets tests assert precise failures instead of string messages.

---

## Validation (Applicative) — *optional, if enabled*

If you’ve pulled in the **0.1.1** changes:

* `Validation`, `Valid`, `Invalid` accumulate *all* errors via `ap`.
* Designed for data validation; there is **no `flatMap`** (applicative focus).

```php
use PenguinPark\Monad\Validation\Validation;

$build = Validation::of(fn ($e) => fn ($a) => fn ($s) => compact('e','a','s'));

$res = $build
  ->ap(vEmail('bad@ex'))   // -> invalid('email: bad format')
  ->ap(vAge(15))           // -> invalid('age: must be ≥ 18')
  ->ap(vSalary(-10));      // -> invalid('salary: must be positive')

$out = $res->fold(fn (array $errs) => $errs, fn ($ok) => $ok);
```

---

## Project status

* Current tag: **0.1.1** (custom exceptions, solid core)
* Next up:

    * **Docs**: keep expanding usage examples and the aliases guide
    * Nice-to-haves: `Either::sequence/traverse`, `IO::sequence/traverse`

---

## Development

```bash
composer test       # phpunit
composer mutate     # infection --min-msi=100
```

CI runs on branch `canon`. Docs-only changes skip CI by default.

---

## Contributing

Issues and PRs welcome. Please include tests. If you’re fixing edge cases in `IO`/`Writer`, mutation tests are your friend.

---

## License

Apache-2.0 — see `LICENSE`.
