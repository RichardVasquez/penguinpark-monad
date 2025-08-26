# Invalid (PenguinPark\Monad\Validation\Invalid)

Concrete Invalid branch of Validation that holds an array of errors and ignores transformations. When combined applicatively, errors accumulate (array_merge).

- Namespace: PenguinPark\Monad\Validation
- Extends: Validation<E, A>
- Type params: E = array<int, mixed> (error collection), A = success value

## Public API

| Method                                                       |          Returns           | Description                                                                |
|:-------------------------------------------------------------|:--------------------------:|:---------------------------------------------------------------------------|
| **__construct**(array&lt;int, mixed&gt; &dollar;errors)      |                            | Create an Invalid with a collection of errors.                             |
| **map**(fn(A):B)                                             |   Validation&lt;E, B&gt;   | No-op for Invalid; returns self.                                           |
| **ap**(Validation&lt;E, B&gt; &dollar;vb)                    | Validation&lt;E, mixed&gt; | Applicative apply that accumulates errors from both sides via array_merge. |
| **fold**(fn(E):R &dollar;onInvalid, fn(A):R &dollar;onValid) |             R              | Deconstruct: calls &dollar;onInvalid with the error collection.            |
| **isValid**()                                                |            bool            | Always false for Invalid.                                                  |
| **errors**()                                                 |  array&lt;int, mixed&gt;   | Accessor for the underlying error collection.                              |

## Notes

- Error accumulation uses array_merge so the error collection behaves as a semigroup.
- Mapping functions are ignored; use fold/getOrElse to extract or provide defaults.

## Example

```php
use PenguinPark\Monad\Validation\Validation;
use PenguinPark\Monad\Validation\Invalid;

$left  = Validation::invalid('E-left');
$right = Validation::invalid('E-right');
$mk    = Validation::of(fn ($a, $b) => [$a, $b]);

$res = $mk->ap($left)->ap($right);
$errs = $res->fold(fn (array $e) => $e, fn ($_) => []); // ['E-left','E-right']

$inv = new Invalid(['e1','e2']);
$all = $inv->errors(); // ['e1','e2']
```
