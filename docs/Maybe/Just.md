# Just (PenguinPark\Monad\Maybe\Just)

Concrete Maybe representing presence of a value

Namespace: PenguinPark\Monad\Maybe
Extends: Maybe&lt;T&gt;

## Public API

| Method                                                                |     Returns     | Description                                                                                         |
|:----------------------------------------------------------------------|:---------------:|:----------------------------------------------------------------------------------------------------|
| **__construct**(mixed &dollar;value)                                  |                 | create a Just with payload T                                                                        |
| **map**(fn(T):U)                                                      | Maybe&lt;U&gt;  | apply function to contained value                                                                   |
| **flatMap**(fn(T) Maybe&lt;U&gt;)                                     | Maybe&lt;U&gt;  | chain to another Maybe; must return Maybe                                                           |
| **getOrElse**(mixed &dollar;default)                                  |      mixed      | returns the contained value (default ignored)                                                       |
| **getOrThrow**(Throwable&vert;string&vert;callable &dollar;throwable) |      mixed      | returns the contained value                                                                         |
| **orElse**(fn(): Maybe&lt;T&gt;)                                      | Maybe&lt;T&gt;  | returns self                                                                                        |
| **filter**(fn(T): bool)                                               | Maybe&lt;T&gt;  | returns self if predicate passes, otherwise Nothing                                                 |
| **tap**(fn(T): void)                                                  |      self       | run effect on value; returns self                                                                   |
| **fold**(fn():X &dollar;onNothing, fn(T):X &dollar;onJust)            |        X        | calls onJust(&dollar;value)                                                                         |
| **orNull**()                                                          |      mixed      | returns the contained value                                                                         |
| **isJust**()                                                          |      bool       | true                                                                                                |
| **isNothing**()                                                       |      bool       | false                                                                                               |
| **apply**(fn(T):void)                                                 |     static      | side-effect only; returns self                                                                      |
| **get**()                                                             |        T        | unsafe unwrap; returns contained value                                                              |
| **__toString**()                                                      |     string      | representation                                                                                      |
| **equals**(mixed)                                                     |      bool       | structural equality (Just with equal payload)                                                       |
| **unit**(mixed &dollar;value)                                         | Just&vert;Maybe | legacy alias to construct Just(&dollar;value). Alias of just()/of() semantics                       |
| **bind**(callable &dollar;function)                                   |      Maybe      | legacy alias of flatMap()                                                                           |
| **ap**(Maybe &dollar;fa)                                              |      Maybe      | Applicative apply expecting this to hold callable to apply to &dollar;fa's Just; Nothing propagates |

## Alias Notes

- **unit**() is an alias of constructing a Just (same as Maybe::**of**/**just**)
- **bind**() is an alias of **flatMap**()
