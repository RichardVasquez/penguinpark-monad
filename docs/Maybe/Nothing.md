# Nothing (PenguinPark\Monad\Maybe\Nothing)

Concrete Maybe representing absence of a value.

- Namespace: PenguinPark\Monad\Maybe
- Extends: Maybe<T>

## Public API

| Method                                                          |     Returns     | Description                                              |
|:----------------------------------------------------------------|:---------------:|:---------------------------------------------------------|
| **__construct**()                                               |                 | create a Nothing.                                        |
| **map**(fn(T):U)                                                |    Maybe\<U>    | returns self (no value to map).                          |
| **flatMap**(fn(T): Maybe\<U>)                                   |    Maybe\<U>    | returns self.                                            |
| **getOrElse**(default&vert;fn():default)                        |      mixed      | returns default; if default is callable, it is invoked.  |
| **getOrThrow**(Throwable&vert;string&vert;callable \$throwable) |      mixed      | always throws; callable must return Throwable.           |
| **orElse**(fn(): Maybe\<T>)                                     |    Maybe\<T>    | calls supplier and returns its Maybe; must return Maybe. |
| **filter**(fn(T): bool)                                         |    Maybe\<T>    | returns self.                                            |
| **tap**(fn(T): void)                                            |      self       | no-op; returns self.                                     |
| **fold**(fn():X \$onNothing, fn(T):X \$onJust)                  |        X        | calls onNothing().                                       |
| **orNull**()                                                    |      null       | returns null.                                            |
| **isJust**()                                                    |      bool       | false.                                                   |
| **isNothing**()                                                 |      bool       | true.                                                    |
| **apply**(fn(T):void)                                           |     static      | side-effect compatibility; no-op; returns self.          |
| **get**()                                                       |      mixed      | always throws when called.                               |
| **__toString**()                                                |     string      | representation.                                          |
| **unit**(mixed \$value)                                         | Just&vert;Maybe | legacy alias to construct a Just($value).                |
| **equals**(mixed)                                               |      bool       | equals any other Nothing.                                |
| **bind**(callable \$function)                                   |     static      | legacy alias of flatMap(); returns self.                 |
| **ap**(Maybe \$fa)                                              |      Maybe      | returns self.                                            |

## Alias Notes

- **unit**() constructs a Just value and is provided for legacy compatibility.
- **bind**() is an alias of **flatMap**().
