# Valid (PenguinPark\Monad\Validation\Valid)

Concrete Valid branch of Validation that holds a success value. Supports mapping and applicative application when containing a callable.

- Namespace: PenguinPark\Monad\Validation
- Extends: Validation<E, A>
- Type params: E = array<int, mixed> (error collection), A = success value

## Public API

| Method                                                       |          Returns           | Description                                                                                                                                 |
|:-------------------------------------------------------------|:--------------------------:|:--------------------------------------------------------------------------------------------------------------------------------------------|
| **__construct**(mixed &dollar;value)                         |                            | Create a Valid wrapping a value.                                                                                                            |
| **map**(fn(A):B)                                             |   Validation&lt;E, B&gt;   | Map a function over the contained value, producing a new Valid.                                                                             |
| **ap**(Validation&lt;E, B&gt; &dollar;vb)                    | Validation&lt;E, mixed&gt; | Applicative apply. Requires this Valid to hold a callable. Applies it to &dollar;vb when &dollar;vb is Valid; accumulates errors otherwise. |
| **fold**(fn(E):R &dollar;onInvalid, fn(A):R &dollar;onValid) |             R              | Deconstruct: calls $onValid with the value.                                                                                                 |
| **isValid**()                                                |            bool            | Always true for Valid.                                                                                                                      |

## Notes

- If Valid::ap is called when the Valid does not contain a callable, it throws \PenguinPark\Monad\Exception\ApCallableExpected.
- For multi-argument callables, Valid::ap supports partial application: if too few args are provided, it returns a new Valid holding a callable awaiting the rest.

## Example

```php
use PenguinPark\Monad\Validation\Validation;

$sum2 = Validation::of(fn (int $a) => fn (int $b) => $a + $b);
$result = $sum2->ap(Validation::of(2))->ap(Validation::of(3));
$val = $result->fold(fn ($e) => null, fn ($a) => $a); // 5
```
