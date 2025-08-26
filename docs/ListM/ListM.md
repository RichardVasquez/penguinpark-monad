# ListM (PenguinPark\Monad\ListM\ListM)

A persistent list monad providing common list/sequence operations, functor/applicative/monad utilities, and conversions  |

- Namespace: PenguinPark\Monad\ListM
- Implements: IteratorAggregate

## Public API
| Method                                                             |             Returns             | Description                                                                                                       |
|:-------------------------------------------------------------------|:-------------------------------:|:------------------------------------------------------------------------------------------------------------------|
| **__construct**(array &dollar;values)                              |                                 | create from array values                                                                                          |
| **fromArray**(array &dollar;values)                                |              self               | construct from array (alias of constructor pattern)                                                               |
| **of**(&dollar;values)                                             |              self               | construct from a single value or array; see implementation semantics. Typically wraps non-array as singleton list |
| **pure**(mixed &dollar;value)                                      |              self               | Applicative pure; singleton list with &dollar;value                                                               |
| **unit**(mixed &dollar;value)                                      |              self               | legacy alias of pure()                                                                                            |
| **empty**()                                                        |              self               | empty list                                                                                                        |
| **map**(fn(A):B)                                                   |              self               | element-wise mapping                                                                                              |
| **flatMap**(fn(A): ListM&lt;B&gt;)                                 |              self               | monadic concatenation of mapped lists                                                                             |
| **bind**(callable &dollar;c)                                       |              self               | alias of flatMap()                                                                                                |
| **filter**(fn(A):bool)                                             |              self               | keep elements satisfying predicate                                                                                |
| **foldLeft**(mixed &dollar;init, fn(acc,A):acc)                    |              mixed              | left fold                                                                                                         |
| **foldRight**(mixed &dollar;init, fn(A,acc):acc)                   |              mixed              | right fold                                                                                                        |
| **append**(self &dollar;other)                                     |              self               | concatenation of two lists                                                                                        |
| **concat**(iterable&lt;ListM&lt;A&gt;&gt; &dollar;lists)           |              self               | flatten a collection of ListM                                                                                     |
| **headOption**()                                                   |         Maybe&lt;A&gt;          | first element as Maybe                                                                                            |
| **lastOption**()                                                   |         Maybe&lt;A&gt;          | last element as Maybe                                                                                             |
| **head**()                                                         |         Maybe&lt;A&gt;          | alias of headOption() semantics                                                                                   |
| **last**()                                                         |         Maybe&lt;A&gt;          | alias of lastOption() semantics                                                                                   |
| **tail**()                                                         |   Maybe&lt;ListM&lt;A&gt;&gt;   | tail of list as Maybe (Nothing for empty or single-element semantics per implementation)                          |
| **isEmpty**()                                                      |              bool               | whether list is empty                                                                                             |
| **count**()                                                        |               int               | number of elements                                                                                                |
| **toArray**()                                                      |              array              | materialize to array                                                                                              |
| **getIterator**()                                                  |           Traversable           | iterator for foreach                                                                                              |
| **apply**(callable &dollar;function)                               |              self               | run side-effect for each element; returns self                                                                    |
| **get**()                                                          |              array              | alias of toArray() semantics; raw array of values                                                                 |
| **equals**(mixed &dollar;other)                                    |              bool               | structural equality by elements                                                                                   |
| **equalsWith**(self &dollar;other, callable &dollar;elemEq)        |              bool               | element-wise equality using predicate                                                                             |
| **__toString**()                                                   |             string              | representation                                                                                                    |
| **sequenceMaybe**()                                                |   Maybe&lt;ListM&lt;A&gt;&gt;   | convert List&lt;Maybe&lt;A&gt;&gt; to Maybe&lt;List&lt;A&gt;&gt; (Nothing if any element is Nothing)              |
| **traverseMaybe**(fn(A): Maybe&lt;B&gt;)                           |   Maybe&lt;ListM&lt;B&gt;&gt;   | map to Maybe and sequence                                                                                         |
| **sequenceEither**()                                               | Either&lt;L, ListM&lt;A&gt;&gt; | convert List&lt;Either&lt;L,A&gt;&gt; to Either&lt;L,List&lt;A&gt;&gt; (first Left short-circuits)                |
| **traverseEither**(fn(A):Either&lt;L,B&gt;)                        | Either&lt;L,ListM&lt;B&gt;&gt;  | map to Either and sequence                                                                                        |
| **ap**(ListM&lt;callable(A):B&gt; &dollar;fa)                      |              self               | Applicative apply across lists (Cartesian application)                                                            |
| **liftA2**(callable &dollar;f, ListM &dollar;fa, ListM &dollar;fb) |              self               | lift binary function over two lists                                                                               |
| **zipWith**(callable &dollar;f, ListM &dollar;other)               |              self               | zip two lists with function until the shorter length                                                              |
| **reduce**(callable &dollar;combine, mixed &dollar;initial)        |              mixed              | reduce/foldLeft alias semantics                                                                                   |
| **reduceRight**(callable &dollar;combine, mixed &dollar;initial)   |              mixed              | reduce/foldRight alias semantics                                                                                  |
| **reduce1**(callable &dollar;combine)                              |              mixed              | reduce non-empty list; may throw if empty                                                                         |
| **reduceL**(mixed &dollar;initial, callable &dollar;combine)       |              mixed              | alias of foldLeft semantics                                                                                       |

## Alias Notes

- **unit**() is a legacy alias of **pure**()
- **bind**() is an alias of **flatMap**()
- **head**()/**last**() behave like **headOption**()/**lastOption**() returning Maybe
- **ge**t() mirrors **toArray**()
- **reduceL**() mirrors **foldLeft**(); **reduce**()/**reduceRight**() reflect reduce semantics
