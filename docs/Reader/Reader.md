# Reader (PenguinPark\Monad\Reader\Reader)

Represents a computation that reads from a shared environment. A Reader<E, A> is essentially a function E -> A wrapped with monadic utilities.

- Namespace: PenguinPark\Monad\Reader

## Public API

| Method                                         | Returns | Description                                                                                               |
|:-----------------------------------------------|:-------:|:----------------------------------------------------------------------------------------------------------|
| **__construct**(Closure \$run)                 |         | create a Reader from a function environment -> value.                                                     |
| **of**(mixed \$value)                          |  self   | construct a Reader that ignores the environment and returns \$value. Alias of pure().                     |
| **reader**(callable \$f)                       |  self   | lift a plain function env->A into Reader.                                                                 |
| **asks**(callable \$f)                         |  self   | alias of reader() semantics; build Reader from a projection of the environment.                           |
| **ask**()                                      |  self   | Reader that returns the environment itself.                                                               |
| **map**(fn(A):B)                               |  self   | transform the produced value, preserving environment usage.                                               |
| **flatMap**(fn(A): Reader\<B>)                 |  self   | sequence computations depending on the environment.                                                       |
| **local**(fn(E):E2)                            |  self   | transform the environment for this Reader (contramap on the input).                                       |
| **run**(mixed \$env)                           |  mixed  | execute the Reader with the given environment.                                                            |
| **pure**(mixed $value)                         |  self   | alias of of(); Applicative pure.                                                                          |
| **bind**(callable \$f)                         |  self   | alias of flatMap().                                                                                       |
| **ap**(self \$fa)                              |  self   | Applicative apply; expects this Reader to produce a callable to apply to \$fa's value under the same env. |
| **liftA2**(callable \$f, self \$fa, self \$fb) |  self   | lift binary function over two Readers.                                                                    |                                                                    
| **__toString**()                               | string  | representation.                                                                                           |

## Alias Notes

- **of**() is an alias of **pure**().
- **pure**() returns a Reader that ignores the environment and yields the value.
- **asks**() is a convenience alias to build a Reader from a projection of the environment (like reader()).
- **bind**() is an alias of **flatMap**().
