# IO (PenguinPark\Monad\IO\IO)

Represents a lazy, referentially transparent description of an effect which can be executed later via **unsafeRun**()/**get**(). Supports mapping, flatMapping, error handling, and Applicative operations

- Namespace | PenguinPark\Monad\IO

## Public API

| Method                                                                             |               Returns               | Description                                                                             |
|:-----------------------------------------------------------------------------------|:-----------------------------------:|:----------------------------------------------------------------------------------------|
| **__construct**(Closure &dollar;thunk)                                             |                                     | create IO from a thunk returning the result when run                                    |
| **of**(mixed &dollar;value)                                                        |                self                 | construct a pure IO producing &dollar;value. Alias of pure()                            |
| **pure**(mixed &dollar;value)                                                      |                self                 | Applicative pure; returns IO that yields &dollar;value                                  |
| **delay**(callable &dollar;thunk)                                                  |                self                 | alias of constructor convenience; lazily capture effect                                 |
| **map**(fn(A):B)                                                                   |                self                 | transform result inside IO                                                              |
| **flatMap**(fn(A): IO&lt;B&gt;)                                                    |                self                 | sequence effects by binding                                                             |
| **bind**(callable &dollar;f)                                                       |                self                 | alias of flatMap()                                                                      |
| **tap**(fn(A):void)                                                                |                self                 | run a side-effect on the value when executed; returns same IO value                     |
| **apply**(fn(A):void)                                                              |                self                 | side-effect only; returns self. Effect runs when IO is executed                         |
| **attempt**()                                                                      | IO&lt;Either&lt;Throwable,A&gt;&gt; | capture exceptions into Either; no exceptions escape when running the returned IO       |
| **mapError**(fn(Throwable):Throwable)                                              |                self                 | map error channel; if evaluation throws, transforms the Throwable                       |
| **handleErrorWith**(fn(Throwable): IO&lt;A&gt;)                                    |                self                 | recover from failures by providing alternative IO                                       |
| **handleError**(fn(Throwable): A)                                                  |                self                 | recover by mapping error to fallback value                                              |
| **unsafeRun**()                                                                    |                mixed                | execute the effect immediately and return the value (side effects occur now)            |
| **get**()                                                                          |                mixed                | alias of unsafeRun()                                                                    |
| **ap**(self &dollar;fa)                                                            |                self                 | Applicative apply; expects this IO to produce a callable to apply to &dollar;fa's value |
| **liftA2**(callable &dollar;f, self &dollar;fa, self &dollar;fb)                   |                self                 | lift a binary function over two IOs                                                     |
| **bracket**(self &dollar;acquire, callable &dollar;use, callable &dollar;release)  |                self                 | resource-safe acquire/use/release pattern, ensuring release runs                        |
| **fromEither**(Either &dollar;e)                                                   |                self                 | convert Either to IO (Left =&gt; throws at run-time, Right =&gt; pure value)            |
| **fromMaybe**(Maybe &dollar;m, Throwable&vert;string &dollar;ifNothing)            |                self                 | convert Maybe to IO (Nothing =&gt; throw supplied error on run, Just =&gt; pure value)  |
| **memoize**()                                                                      |                self                 | cache result of this IO so subsequent unsafeRun() calls reuse the value/effect outcome  |
| **__toString**()                                                                   |               string                | debugging representation                                                                |

Alias notes
- **of**() is an alias of **pure**()
- **get**() is an alias of **unsafeRun**()
- **delay**() is a convenience alias of the constructor pattern (IO::**of** and closures); it defers effect creation
