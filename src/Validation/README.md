# Validation — Accumulating errors (Applicative)

Validation&lt;E, A&gt; represents a computation that can be Valid(A) or Invalid(E),
where E is an error collection. Unlike Either (which short-circuits), Validation
supports applicative combination that accumulates all errors using a semigroup
(here: PHP arrays combined via array_merge).

## Classes
- [Validation<E, A>](../../docs/Validation/Validation.md) - abstract base with map/ap/fold helpers
- [Valid<E, A>](../../docs/Validation/Valid.md) - success branch holding a value A
- [Invalid<E, A>](../../docs/Validation/Invalid.md) - failure branch holding an array of errors E

## Key capabilities
- Lift/construct: of(a) creates Valid(a); invalid(err&vert;errs) creates Invalid(&lbrack;errs...&rbrack;)
- Transformations: map(fn A-&gt;B) over success value; Invalid ignores map
- Applicative: ap(Validation&lt;E, B&gt;) applies a function inside Valid to another Validation, accumulating errors from either side
  - Supports partial application when using multi-argument callables (curry style)
- Deconstruct/access: fold(onInvalid, onValid), getOrElse(default&vert;fn(E):A), isValid/isInvalid
- Error inspection: Invalid::errors(): array

## Interoperability with other modules
- Either: Use Validation when you need error accumulation (e.g., form validation). Use Either for short-circuiting. Conversions can be ad-hoc via fold.
- Maybe: Convert Maybe to Validation with an error when absent; use fold/getOrElse to move between them.
- IO/Writer/ListM/Reader/State: Validation values can be carried inside these contexts and combined applicatively where appropriate.

## Examples
1) Basic map and fold
```php
use PenguinPark\Monad\Validation\Validation;

$v = Validation::of(2)
    ->map(fn (int $x) => $x + 1);

$out = $v->fold(fn (array $errs) => null, fn ($a) => $a); // 3
```

2) Applicative combination with error accumulation
```php
use PenguinPark\Monad\Validation\Validation;

// Use a curried function for multi-arg application
$sum2 = Validation::of(fn (int $a) => fn (int $b) => $a + $b);

$result = $sum2
    ->ap(Validation::of(2))
    ->ap(Validation::of(3));

$val = $result->fold(fn ($e) => null, fn ($a) => $a); // 5
```

3) Accumulating from both sides
```php
$mkPair = Validation::of(fn ($a, $b) => [$a, $b]);

$left  = Validation::invalid('E-left');
$right = Validation::invalid('E-right');

$res = $mkPair->ap($left)->ap($right);

$errs = $res->fold(fn (array $e) => $e, fn ($_) => []);
// ['E-left', 'E-right']
```

4) Using getOrElse with a function default
```php
$ok = Validation::of(42)->getOrElse(0); // 42

$bad = Validation::invalid(['e1','e2'])
    ->getOrElse(fn (array $errs) => count($errs)); // 2
```

Notes
- Valid::ap expects the Valid to contain a callable. If it doesn't, an ApCallableExpected exception is thrown.
- Error collection type E is an array; accumulation uses array_merge (semigroup behavior).
