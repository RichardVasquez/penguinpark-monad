# Writer — Values with logs

Writer&lt;W, A&gt; pairs a value A with a monoidal log W (here the log is an array and the monoid is concatenation). It allows accumulating auxiliary information (e.g., debug entries) alongside computations.

## Classes
- [Writer&lt;A&gt;](../../docs/Writer/Writer.md) - concrete writer with array log ([], array_merge) per implementation

## Key capabilities
- Logging primitives: of(a) (empty log), tell(w) (append log)
- Transformations: map (value), flatMap (concatenates logs)
- Observing/modifying log: listen(), listens(fn log-&gt;b), pass(), censor(fn log-&gt;log)
- Running: run(): &lbrack;log, value&rbrack;, log(), value()/get()
- Applicative: ap, liftA2, pure/unit

## Interoperability with other monads
- Maybe/Either: Build computations that both log and may fail; map to Writer&lt;Either&gt; or Either&lt;Writer&gt; depending on needs.
- IO: Emit logs in pure space, then at the boundary, convert to IO to print/persist the log.
- ListM: Produce multiple writers and combine them; logs concatenate.
- Reader/State: Combine environment/state with logging; e.g., State transitions that also log.

## Examples
1) Basic logging pipeline
```php
use PenguinPark\Monad\Writer\Writer;

$w = Writer::of(1)
    ->flatMap(fn($n) => Writer::tell('start')->map(fn() => $n))
    ->map(fn($n) => $n * 2)
    ->flatMap(fn($n) => Writer::tell("doubled to $n")->map(fn() => $n));

[$log, $val] = $w->run();
// $log like ['start', 'doubled to 2']; $val = 2
```

2) Using listen and censor
```php
$withLogVal = $w->listen();     // value contains [log, A] (see concrete shape)
$redacted   = $w->censor(fn(array $log) => array_map('strtoupper', $log));
```

3) Applicative combine
```php
$mk = fn($a, $b) => [$a, $b];
$wa = Writer::of(3)->flatMap(fn($n) => Writer::tell('a')->map(fn() => $n));
$wb = Writer::of(4)->flatMap(fn($n) => Writer::tell('b')->map(fn() => $n));
$pair = Writer::liftA2($mk, $wa, $wb);
[$log, $val] = $pair->run(); // log merges: ['a','b']; value [3,4]
```
