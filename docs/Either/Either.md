# Either (PenguinPark\Monad\Either\Either)

Disjoint union type representing a value of one of two possible types (a sum type). Left is conventionally used for the error/alternative path, Right for the success path. This type is right-biased: map/flatMap/tap operate on the Right branch.

- Namespace: PenguinPark\Monad\Either
- Type params: L (Left), R (Right)
- Concrete subclasses: Left, Right

## Public API

| Method                                                                      |          Returns          | Description                                                                                                                  |
|:----------------------------------------------------------------------------|:-------------------------:|:-----------------------------------------------------------------------------------------------------------------------------|
| **map**(fn(R):U)                                                            |     Either&lt;L,U&gt;     | map the Right value; Left passes through unchanged.                                                                          |
| **flatMap**(fn(R): Either&lt;L,U&gt;)                                       |     Either&lt;L,U&gt;     | chain computations on Right; returns the produced Either.                                                                    |
| **mapLeft**(fn(L):M)                                                        |     Either&lt;M,R&gt;     | map the Left value; Right passes through unchanged.                                                                          |
| **fold**(onLeft: fn(L):X, onRight: fn(R):X)                                 |             X             | deconstruct Either by providing both handlers.                                                                               |
| **getOrElse**(default&vert;fn():default)                                    |      R&vert;default       | unwrap Right or produce default for Left.                                                                                    |
| **orElse**(fn(): Either&lt;L,R&gt;)                                         |     Either&lt;L,R&gt;     | on Left, lazily produce a replacement Either; on Right, returns self.                                                        |
| **isLeft**()                                                                |           bool            | whether this is a Left.                                                                                                      |
| **isRight**()                                                               |           bool            | whether this is a Right.                                                                                                     |
| **equals**(mixed)                                                           |           bool            | structural equality (branch + payload equality using value semantics).                                                       |
| **swap**()                                                                  |     Either&lt;R,L&gt;     | swap branches: Left→Right, Right→Left.                                                                                       |
| **apply**(fn(R):void)                                                       |           self            | side-effect with Right value; returns self for chaining. No-op on Left.                                                      |
| **get**()                                                                   |           mixed           | unsafe unwrap for debugging; Right returns value; Left may throw in subclass.                                                |
| **bimap**(fn(L):M, fn(R):U)                                                 |     Either&lt;M,U&gt;     | map both sides at once.                                                                                                      |
| **getRigh**t()                                                              |      Maybe&lt;R&gt;       | Right payload as Maybe (Just on Right, Nothing on Left).                                                                     |
| **getLeft**()                                                               |      Maybe&lt;L&gt;       | Left payload as Maybe (Just on Left, Nothing on Right).                                                                      |
| **toMaybeRight**()                                                          |      Maybe&lt;R&gt;       | alias of **getRight**() in semantics; convert Right to Maybe.                                                                |
| **toMaybeLeft**()                                                           |      Maybe&lt;L&gt;       | alias of **getLeft**() in semantics; convert Left to Maybe.                                                                  |
| **rightOrNull**()                                                           |        R&vert;null        | Right value or null when Left.                                                                                               |
| **leftOrNull**()                                                            |        L&vert;null        | Left value or null when Right.                                                                                               |
| **getRightOrElse**(default&vert;fn():default)                               |      R&vert;default       | Right or default.                                                                                                            |
| **getLeftOrElse**(default&vert;fn():default)                                |      L&vert;default       | Left or default.                                                                                                             |
| **tap**(fn(R):void)                                                         |           self            | side-effect on Right, returns self. Alias in spirit of **apply**().                                                          |
| **tapLeft**(fn(L):void)                                                     |           self            | side-effect on Left, returns self.                                                                                           |
| **orThrow**(fn(L):Throwable)                                                |             R             | unwrap Right or throw an exception mapped from Left.                                                                         |
| **ap**(Either&lt;L, callable(A):B&gt; &dollar;fa)                           |     Either&lt;L,B&gt;     | Applicative apply; requires this to hold a callable in Right.                                                                |
| **right**(mixed &dollar;value)                                              |           self            | construct a Right value. Alias of Right::**of**().                                                                           |
| **left**(mixed &dollar;value)                                               |           self            | construct a Left value. Alias of Left::**of**().                                                                             |
| **pure**(mixed &dollar;x)                                                   | Either&lt;never,mixed&gt; | Applicative pure; constructs Right(&dollar;x). Alias of **right**().                                                         |
| **liftA2**(callable &dollar;f, Either &dollar;fa, Either &dollar;fb)        |          Either           | lift binary function to Applicative.                                                                                         |
| **tryCatch**(callable &dollar;thunk, ?callable &dollar;mapThrowable = null) |           self            | run a thunk; capture Throwable as Left, success as Right. If &dollar;mapThrowable is provided, map Throwable to Left value.  |
| **try**(callable &dollar;thunk, ?callable &dollar;mapThrowable = null)      |           self            | alias of **tryCatch**().                                                                                                     |
| **bind**(callable &dollar;f)                                                |           self            | legacy alias of **flatMap**().                                                                                               |
| **of**(mixed &dollar;value)                                                 |           self            | alias of **right**().                                                                                                        |
| **unit**(mixed &dollar;value)                                               |           self            | legacy alias of **right**()/**of**().                                                                                        |

## Alias Notes

- **left**() is an alias of Left::**of**().
- **right**() is an alias of Right::**of**().
- **try**() is an alias of **tryCatch**()
- **bind**() is a legacy alias of **flatMap**()
- **of**() is an alias of **right**()
- **unit**() is a legacy alias of **of**().

## Notes

- Right-biased: **map**/**flatMap**/**tap** focus on Right. Left propagates unchanged.
- orElse supplier must return an Either; otherwise an exception is thrown by concrete subclasses.
- ap expects the receiver to hold a callable when it is Right; otherwise throws.

## Examples

```php
use PenguinPark\Monad\Either\Either;

&dollar;parse = fn(string &dollar;s) =&gt; is_numeric(&dollar;s)
    ? Either::right((int)&dollar;s)
    : Either::left('NaN');

&dollar;res = &dollar;parse('10')
    -&gt;map(fn(&dollar;n) =&gt; &dollar;n + 5)
    -&gt;flatMap(fn(&dollar;n) =&gt; &dollar;n &gt; 10 ? Either::right(&dollar;n) : Either::left('too small'));
```