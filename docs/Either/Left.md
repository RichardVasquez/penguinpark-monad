# Left (PenguinPark\Monad\Either\Left)

Represents the Left branch of Either, conventionally used for an error or alternate computation path.

- Namespace: PenguinPark\Monad\Either
- Extends: Either&lt;L,R&gt;

## Public API

| Method                                                                |                Returns                | Description                                                                 |
|:----------------------------------------------------------------------|:-------------------------------------:|:----------------------------------------------------------------------------|
| **__construct**(mixed &dollar;value)                                  |                                       | create a Left with payload L.                                               |
| **map**(fn(R):U)                                                      |           Either&lt;L,U&gt;           | no-op on Left; returns self.                                                |
| **flatMap**(fn(R)                                                     | Either&lt;L,U&gt;): Either&lt;L,U&gt; | no-op on Left; returns self.                                                |
| **mapLeft**(fn(L):M)                                                  |           Either&lt;M,R&gt;           | maps the Left payload using fn.                                             |
| **fold**(onLeft: fn(L):X, onRight: fn(R):X)                           |                   X                   | calls onLeft(&dollar;value).                                                |
| **getOrElse**(default&vert;fn():default)                              |                 mixed                 | returns default (or result of supplier) since this is Left.                 |
| **orElse**(fn(): Either&lt;L,R&gt;)                                   |           Either&lt;L,R&gt;           | calls supplier and returns its Either (must return Either; else exception). |
| **isLeft**()                                                          |                 bool                  | true.                                                                       |
| **isRight**()                                                         |                 bool                  | false.                                                                      |
| **swap**()                                                            |           Either&lt;R,L&gt;           | converts to Right(&dollar;value).                                           |
| **apply**(fn(R):void)                                                 |                 self                  | side-effect method; no-op on Left; returns self.                            |
| **get**()                                                             |                 mixed                 | unsafe unwrap of Left payload; may be used for debugging/inspection.        |
| **equals**(mixed)                                                     |                 bool                  | structural equality: Left equals other Left with equal payload.             |
| **__toString**()                                                      |                string                 | string representation.                                                      |
| **tap**(fn(R):void)                                                   |                 self                  | no-op on Left; returns self.                                                |
| **tapLeft**(fn(L):void)                                               |                 self                  | runs effect on Left payload; returns self.                                  |
| **orThrow**(fn(L):Throwable)                                          |                 mixed                 | throws the mapped exception for Left.                                       |
| **ap**(Either&lt;L,A&gt; &dollar;fa)                                  |           Either&lt;L,B&gt;           | propagates Left unchanged.                                                  |
| **getLeft**()                                                         |            Maybe&lt;L&gt;             | Just(&dollar;value).                                                        |
| **getRight**()                                                        |            Maybe&lt;R&gt;             | Nothing.                                                                    |
| **getOrThrow**(Throwable&vert;string&vert;callable &dollar;throwable) |                 mixed                 | throws; callable must return Throwable.                                     |
| **of**(mixed &dollar;value)                                           |                 self                  | alias: construct Left; same as new Left(&dollar;value).                     |
| **unit**(mixed &dollar;value)                                         |                 self                  | legacy alias of of().                                                       |

## Alias notes
- 
- **of**() is an alias of the constructor for convenience.
- **unit**() is a legacy alias of **of**().
