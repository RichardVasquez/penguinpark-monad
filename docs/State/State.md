# State (PenguinPark\Monad\State\State)

Represents a pure state transition S -> (S, A). Provides stateful computations with functional composition, functor/applicative/monad operations.

- Namespace: PenguinPark\Monad\State

Public API

| Method                                         |     Return     | Description                                                                            |
|:-----------------------------------------------|:--------------:|:---------------------------------------------------------------------------------------|
| **of**(mixed \$a)                              |   self<S,A>    | build a state that yields value A without changing state.                              |                           
| **getState**()                                 |   self<S,S>    | alias of get(); returns current state as value, without changing it.                   |                
| **setState**(mixed \$s)                        |  self<S,null>  | alias of put(\$s); sets the state to \$s, yields null.                                 |                              
| **get**()                                      |   self<S,S>    | yield current state as value; state unchanged.                                         |                                     
| **put**(mixed \$s)                             |  self<S,null>  | set state to \$s; value is null.                                                       |                                                   
| **modify**(fn(S):S)                            |  self<S,null>  | update state using function; yields null.                                              |                                           
| **gets**(fn(S):A)                              |   self<S,A>    | derive a value from current state; state unchanged.                                    |                                  
| **map**(fn(A):B)                               |   self<S,B>    | map the result value; state threading preserved.                                       |
| **flatMap**(fn(A): State<S,B>)                 |   self<S,B>    | chain stateful computations.                                                           |                                                    
| **run**(mixed \$s)                             | array{0:S,1:A} | run with initial state; returns tuple \[newState, value].                              |
| **eval**(mixed \$s)                            |     mixed      | run and return only the value A.                                                       |
| **exec**(mixed \$s)                            |     mixed      | run and return only the new state S.                                                   |
| **pure**(mixed \$a)                            |      self      | alias of of().                                                                         |
| **bind**(callable \$f)                         |      self      | alias of flatMap().                                                                    |
| **apply**(callable \$effect)                   |      self      | side-effect on the current value when run; returns self (state threading preserved).   |
| **ap**(self \$fa)                              |      self      | Applicative apply; expects this State to yield a callable to apply to \$fa's value.    |
| **liftA2**(callable \$f, self \$fa, self \$fb) |      self      | lift binary function over two States.                                                  |
| **__toString**()                               |     string     | representation.                                                                        |

## Alias notes
- **getState**() is an alias of **get**().
- **setState**(\$s) is an alias of **put**(\$s).
- **pure**() is an alias of **of**().
- **bind**() is an alias of **flatMap**().
