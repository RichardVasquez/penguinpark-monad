# Writer (PenguinPark\Monad\Writer\Writer)

Writer monad accumulates a log alongside a computed value. A Writer<W, A> pairs a monoidal log (array here) with a value.

- Namespace: PenguinPark\Monad\Writer

## Public API

| Method                                         |          Return           | Description                                                                                                           |
|:-----------------------------------------------|:-------------------------:|:----------------------------------------------------------------------------------------------------------------------|
| **__construct**(array \$log, mixed \$value)    |                           | construct Writer with initial log and value.                                                                          |
| **of**(mixed \$a)                              |           self            | construct Writer with empty log and value $a.                                                                         |
| **tell**(mixed \$w)                            |           self            | produce Writer that appends \$w to the log; value is null (conventional effect).                                      |                             
| **map**(fn(A):B)                               |           self            | map value, log unchanged.                                                                                             |                                                                                       
| **flatMap**(fn(A) Writer)                      |           self            | chain computations, concatenating logs.                                                                               |                                                               
| **listen**()                                   |           self            | expose current log in the value (value becomes \[log, A] or per implementation).                                      |                                   
| **listens**(fn(array) mixed)                   |           self            | transform what part of the log to expose alongside the value.                                                         |                                      
| **pass**()                                     |           self            | given a Writer<array(callable), A>, modify the log using a provided function; see implementation for exact structure. | 
| **censor**(fn(array): array)                   |           self            | transform the log post-computation.                                                                                   |                                                        
| **run**()                                      | array{0: array, 1: mixed} | get pair of \[log, value].                                                                                            |                                                                
| **log**()                                      |           array           | get the accumulated log.                                                                                              |                                                                                      
| **value**()                                    |           mixed           | get the value only.                                                                                                   |                                                                                         
| **pure**(mixed \$a)                            |           self            | alias of of().                                                                                                        |                                                                                       
| **unit**(mixed \$a)                            |           self            | legacy alias of of().                                                                                                 |                                                                                
| **bind**(callable $f)                          |           self            | alias of flatMap().                                                                                                   |                                                                                
| **apply**(callable $effect)                    |           self            | side-effect on the current value; returns self.                                                                       |                                              
| **get**()                                      |           mixed           | alias of value() semantics; returns value.                                                                            |                                                                    
| **ap**(self \$fa)                              |           self            | Applicative apply; expects this Writer to hold a callable to apply to \$fa's value, logs concatenated.                | 
| **liftA2**(callable \$f, self \$fa, self \$fb) |           self            | lift binary function; logs accumulate.                                                                                |
| **__toString**()                               |          string           | representation.                                                                                                       |

## Alias Notes
- pure() and unit() alias of of().
- bind() is an alias of flatMap().
- get() mirrors value().
