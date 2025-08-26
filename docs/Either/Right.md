# Right (PenguinPark\Monad\Either\Right)

Represents the Right branch of Either, conventionally used for the success path. Right-biased monadic operations operate on this branch.

- Namespace: PenguinPark\Monad\Either
- Extends: Either&lt;L,R&gt;

## Public API

| Method                                                                               |      Returns      | Description                                                                               |
|:-------------------------------------------------------------------------------------|:-----------------:|:------------------------------------------------------------------------------------------|
| **__construct**(mixed &dollar;value)                                                 |                   | create a Right with payload R.                                                            |
| **map**(fn(R):U)                                                                     | Either&lt;L,U&gt; | maps the Right payload.                                                                   |
| **flatMap**(fn(R): Either&lt;L,U&gt;)                                                | Either&lt;L,U&gt; | chain computation on Right.                                                               |
| **mapLeft**(fn(L):M)                                                                 | Either&lt;M,R&gt; | no-op on Right; returns self.                                                             |
| **fold**(onLeft: fn(L):X, onRight: fn(R):X)                                          |         X         | calls onRight(&dollar;value).                                                             |
| **getOrElse**(default&vert;fn():default)                                             |       mixed       | returns the Right value.                                                                  |
| **orElse**(fn(): Either&lt;L,R&gt;)                                                  | Either&lt;L,R&gt; | returns self.                                                                             |
| **isLeft**()                                                                         |       bool        | false.                                                                                    |
| **isRight**()                                                                        |       bool        | true.                                                                                     |
| **swap**()                                                                           | Either&lt;R,L&gt; | converts to Left(&dollar;value).                                                          |
| **apply**(fn(R):void)                                                                |       self        | run effect with Right payload; returns self.                                              |
| **get**()                                                                            |       mixed       | unsafe unwrap; returns Right value.                                                       |
| **equals**(mixed)                                                                    |       bool        | structural equality: Right equals other Right with equal payload.                         |
| **__toString**()                                                                     |      string       | representation.                                                                           |
| **tap**(fn(R):void)                                                                  |       self        | run effect on Right; returns self.                                                        |
| **tapLeft**(fn(L):void)                                                              |       self        | no-op on Right; returns self.                                                             |
| **orThrow**(fn(L):Throwable)                                                         |       mixed       | returns Right value (no throw).                                                           |
| **ap**(Either&lt;L,A&gt; &dollar;fa)                                                 | Either&lt;L,B&gt; | expects this to hold callable; applies to &dollar;fa's Right value; else propagates Left. |
| **getLeft**()                                                                        |  Maybe&lt;L&gt;   | Nothing.                                                                                  |
| **getRight**()                                                                       |  Maybe&lt;R&gt;   | Just(&dollar;value).                                                                      |
| **getOrThrow**(Throwable&vert;string&vert;callable &dollar;throwable = 'Left value') |       mixed       | returns Right value.                                                                      |
| **of**(mixed &dollar;value)                                                          |       self        | alias: construct Right; same as new Right(&dollar;value).                                 |
| **unit**(mixed &dollar;value)                                                        |       self        | legacy alias of of().                                                                     |

## Alias Notes

- **of**() is an alias of the constructor for convenience.
- **unit**() is a legacy alias of **of**().
