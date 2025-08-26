# ListM — List functor/applicative/monad

A persistent list monad providing list/sequence operations and conversions to/from other monads.

## Classes
- [ListM&lt;A&gt;](../../docs/ListM/ListM.md) - immutable sequence with many utilities; implements IteratorAggregate

## Key capabilities
- Construction: fromArray, of/pure (singleton), empty
- Transformations: map, filter, flatMap (monadic bind), append/concat
- Folds/Reductions: foldLeft/foldRight, reduce/reduceRight/reduce1
- Queries: isEmpty, count, headOption/lastOption/tail
- Traversals: sequenceMaybe/traverseMaybe, sequenceEither/traverseEither
- Applicative: ap (Cartesian), liftA2
- Introspection: equals/equalsWith, toArray/get, getIterator

## Interoperability with other monads
- Maybe: 
  - headOption()/lastOption() expose elements safely as Maybe
  - traverseMaybe(fn A-&gt;Maybe B) produces Maybe&lt;ListM&lt;B&gt;&gt;; short-circuits to Nothing if any element is Nothing
- Either:
  - traverseEither(fn A-&gt;Either L B) produces Either&lt;L, ListM&lt;B&gt;&gt;; first Left short-circuits
- IO:
  - Map ListM&lt;IO&lt;A&gt;&gt; to IO&lt;List&lt;A&gt;&gt; using a custom sequence pattern; or combine IOs with liftA2/ap
- Reader/State/Writer:
  - Useful for bulk computations: map/traverse produce combined results inside those monads

## Examples
1) Basic map/filter/flatMap
```php
use PenguinPark\Monad\ListM\ListM;

$list = ListM::fromArray([1,2,3]);
$evensTimesTen = $list
    -&gt;filter(fn($n) => $n % 2 === 0)
    -&gt;map(fn($n) => $n * 10);           // [20]

$withNeighbors = $list->flatMap(fn($n) => ListM::fromArray([$n-1, $n, $n+1]));
```

2) Traverse with Maybe
```php
use PenguinPark\Monad\Maybe\Maybe;

$parse = fn(string $s) => is_numeric($s)
    ? Maybe::just((int)$s)
    : Maybe::nothing();

$xs = ListM::fromArray(['1','2','x']);
$res = $xs->traverseMaybe($parse); // Nothing (because of 'x')

$ys = ListM::fromArray(['3','4']);
ok  = $ys->traverseMaybe($parse); // Just(ListM([3,4]))
```

3) Traverse with Either
```php
use PenguinPark\Monad\Either\Either;

$parseE = fn(string $s) => is_numeric($s)
    ? Either::right((int)$s)
    : Either::left("NaN: $s");

$nums = ListM::fromArray(['1','x','3']);
$acc  = $nums->traverseEither($parseE); // Left('NaN: x')
```
