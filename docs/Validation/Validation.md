# Validation (PenguinPark\Monad\Validation\Validation)

Applicative validation that can be Valid(A) or Invalid(E). Unlike Either, Validation is designed to accumulate errors when combining computations applicatively.

- Namespace: PenguinPark\Monad\Validation
- Type params: E = array<int, mixed> (error collection), A = success value
- Concrete subclasses: Valid, Invalid

## Public API

| Method                                                       |          Returns           | Description                                                                                                      |
|:-------------------------------------------------------------|:--------------------------:|:-----------------------------------------------------------------------------------------------------------------|
| **of**(mixed $value)                                         |     Valid&lt;E, A&gt;      | Lift a pure value into a Valid.                                                                                  |
| **invalid**(array&lt;int, mixed&gt;&vert;mixed &dollar;err)  |    Invalid&lt;E, A&gt;     | Create an Invalid from one error or an array of errors. Scalars are wrapped into a single-element array.         |
| **map**(fn(A):B)                                             |   Validation&lt;E, B&gt;   | Map a function over the Valid value; Invalid ignores the mapping (no-op).                                        |
| **ap**(Validation&lt;E, B&gt; &dollar;vb)                    | Validation&lt;E, mixed&gt; | Applicative apply; if both sides are Valid with a callable, applies it; errors from either side are accumulated. |
| **fold**(fn(E):R &dollar;onInvalid, fn(A):R &dollar;onValid) |             R              | Deconstruct the Validation by providing handlers for Invalid and Valid.                                          |
| **getOrElse**(A&vert;fn(E):A $default)                       |             A              | Extract the value or, if Invalid, return default or compute it from the errors.                                  |
| **isValid**()                                                |            bool            | Whether the instance represents a Valid value.                                                                   |
| **isInvalid**()                                              |            bool            | Negation of isValid(); true when Invalid.                                                                        |

## Notes

- Error collection type E is an array; accumulation uses array_merge (semigroup behavior).
- Prefer Validation for scenarios like form validation where you want to collect all errors rather than short-circuit on the first.
- ap expects the Valid side to contain a callable; otherwise a \PenguinPark\Monad\Exception\ApCallableExpected may be thrown by Valid::ap.

## Examples

```php
use PenguinPark\Monad\Validation\Validation;

// 1) Basic mapping
$val = Validation::of(2)->map(fn (int $x) => $x + 1);
$result = $val->fold(fn (array $e) => null, fn ($a) => $a); // 3

// 2) Applicative combination with error accumulation
$sum2 = Validation::of(fn (int $a) => fn (int $b) => $a + $b);
$res  = $sum2->ap(Validation::of(2))->ap(Validation::of(3));
$ok   = $res->fold(fn ($e) => null, fn ($a) => $a); // 5

// 3) Accumulating errors
$mkPair = Validation::of(fn ($a, $b) => [$a, $b]);
$left   = Validation::invalid('E-left');
$right  = Validation::invalid('E-right');
$errs   = $mkPair->ap($left)->ap($right)
    ->fold(fn (array $e) => $e, fn ($_) => []); // ['E-left','E-right']

// 4) getOrElse with function default
$missing = Validation::invalid(['e1','e2'])
    ->getOrElse(fn (array $errs) => count($errs)); // 2
```
