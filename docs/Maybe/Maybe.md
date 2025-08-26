# Maybe (PenguinPark\Monad\Maybe\Maybe)

Optional value container: Just(value) or Nothing. Supports functor/applicative/monad interfaces.

- Namespace: PenguinPark\Monad\Maybe
- Concrete subclasses: Just, Nothing

## Public API


| Method                                                             |      Returns       | Description                                                                                      |
|:-------------------------------------------------------------------|:------------------:|:-------------------------------------------------------------------------------------------------|
| **map**(fn(A):B)                                                   |   Maybe&lt;B&gt;   | transform value if Just; Nothing remains Nothing.                                                |
| **flatMap**(fn(A): Maybe&lt;B&gt;)                                 |   Maybe&lt;B&gt;   | chain computations; function must return Maybe.                                                  |
| **getOrElse**(default&vert;fn():default)                           |   A&vert;default   | unwrap Just or default for Nothing (supplier lazily evaluated).                                  |
| **getOrThrow**(Throwable&vert;string&vert;fn():Throwable)          |         A          | unwrap or throw provided/constructed exception when Nothing.                                     |
| **orElse**(fn(): Maybe&lt;A&gt;)                                   |   Maybe&lt;A&gt;   | on Nothing, lazily supply alternative Maybe.                                                     |
| **filter**(fn(A): bool)                                            |   Maybe&lt;A&gt;   | keep Just only if predicate passes; otherwise Nothing.                                           |
| **tap**(fn(A): void)                                               |        self        | side-effect on Just; returns self.                                                               |
| **fold**(fn():X &dollar;onNothing, fn(A):X &dollar;onJust)         |         X          | deconstruct.                                                                                     |
| **orNull**()                                                       |    A&vert;null     | Just value or null.                                                                              |
| **isJust**()                                                       |        bool        | whether value is present.                                                                        |
| **isNothing**()                                                    |        bool        | whether value is absent.                                                                         |
| **equals**(mixed)                                                  |        bool        | structural equality.                                                                             |
| **just**(mixed &dollar;value)                                      |       Maybe        | construct Just(&dollar;value).                                                                   |
| **nothing**()                                                      |       Maybe        | construct Nothing.                                                                               |
| **fromValue**(mixed &dollar;value)                                 |        self        | Nothing if value is a special sentinel per implementation; otherwise Just.                       |
| **fromNullable**(mixed &dollar;value)                              |       Maybe        | Just if value !== null, else Nothing.                                                            |
| **of**(mixed &dollar;value)                                        |       Maybe        | alias of just().                                                                                 |
| **ap**(Maybe&lt;callable(A):B&gt; &dollar;fa)                      |   Maybe&lt;B&gt;   | Applicative apply; Just(callable) applied to Just(value) yields Just(result), otherwise Nothing. |
| **pure**(mixed &dollar;x)                                          |       Maybe        | alias of just(); Applicative pure.                                                               |
| **liftA2**(callable &dollar;f, Maybe &dollar;fa, Maybe &dollar;fb) |       Maybe        | lift binary function over two Maybe values.                                                      |
| **toEither**(mixed &dollar;ifNothing)                              | Either&lt;L,A&gt;  | convert to Either (Nothing =&gt; Left(&dollar;ifNothing), Just =&gt; Right(&dollar;value)).      |
| **fromEither**(Either &dollar;e)                                   |       Maybe        | Right =&gt; Just, Left =&gt; Nothing.                                                            |
| **sequenceArray**(array &dollar;ms)                                | Maybe&lt;array&gt; | sequence array of Maybe into Maybe array (Nothing if any element is Nothing).                    |
| **traverseArray**(array &dollar;xs, callable &dollar;f)            | Maybe&lt;array&gt; | map to Maybe and sequence.                                                                       |

## Alias Notes

- **of**() is an alias of **just**().
- **pure**() is an alias of **just**().
