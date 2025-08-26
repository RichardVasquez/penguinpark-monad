# State — Pure stateful computations

State&lt;S, A&gt; represents a pure state transition function S -&gt; (S, A). It enables stateful programming while remaining pure by threading state explicitly.

## Classes
- [State&lt;S,A&gt;](../../docs/State/State.md) - single class wrapping the state transition

## Key capabilities
- Construction/primitives: of(a), get(), put(s), modify(fn S-&gt;S), gets(fn S-&gt;A)
- Running: run(&dollar;s): &lbrack;S,A&rbrack;, eval(&dollar;s): A, exec(&dollar;s): S
- Functor/Monad/Applicative: map, flatMap, ap, liftA2, pure
- Utilities: getState()/setState() aliases, apply(fn A-&gt;void) for side-effects during run

## Interoperability with other monads
- Maybe/Either: You can carry state while computations may fail; e.g., flatMap returns State that internally returns Maybe/Either, or use ListM traversals producing State.
- ListM: Use ListM to build sequences of stateful updates, or traverse a list with a function returning State.
- IO: Run State to produce a value/state tuple and then feed into IO; or build State transitions that return IO which you execute later.
- Writer/Reader: Combine logging (Writer) with mutable state (State), or use environment (Reader) to influence transitions via gets/local patterns.

## Examples
1) Counter
```php
use PenguinPark\Monad\State\State;

$inc = State::modify(fn(int $n) => $n + 1);
$get = State::get();

$program = $inc->flatMap(fn() => $get);
[$s2, $value] = $program->run(0); // [1, 1]
```

2) Accumulate while mapping (eval/exec)
```php
$double = State::get()->map(fn($n) => $n * 2);
[$s, $a] = $double->run(5); // [5, 10]
$onlyValue = $double->eval(5); // 10
$onlyState = $double->exec(5); // 5
```

3) Using gets and modify
```php
// Pop the head of an array-stack, returning the popped value
$pop = State::gets(fn(array $s) => $s[0] ?? null)
    ->flatMap(fn($top) => State::modify(fn(array $s) => array_slice($s, 1))
        ->map(fn() => $top));
```
