# Maybe — Optional values

Maybe&lt;T&gt; represents an optional value: Just(T) or Nothing. It is a right-biased functor/applicative/monad over the present value.

## Classes
- [Maybe&lt;E, A&gt;](../../docs/Maybe/Maybe.md) - abstract base
- [Just&lt;E, A&gt;](../../docs/Maybe/Just.md) - value present
- [Nothing&lt;E, A&gt;](../../docs/Maybe/Nothing.md) - value absent

## Key capabilities
- Safe transformations: map, flatMap
- Safe access: getOrElse, getOrThrow, orNull, fold, orElse
- Predicates: isJust, isNothing, filter
- Applicative: pure/just, ap, liftA2
- Interop: toEither(&dollar;ifNothing), fromEither(Either)

## Interoperability with other monads
- Either: Maybe::toEither(&dollar;ifNothing) converts Nothing to Left(&dollar;ifNothing) and Just to Right. Either::getRight()/getLeft() return Maybe views.
- IO: IO::fromMaybe(Maybe, &dollar;ifNothing) turns Just into pure IO, Nothing into a failing IO that throws when run.
- ListM: ListM::traverseMaybe(fn A-&gt;Maybe B) and sequenceMaybe() aggregate across lists, short-circuiting to Nothing on any failure.
- Reader/State/Writer: You can thread Maybe results through these contexts or lift their results into Maybe with domain logic.

## Examples
1) Basic operations
```php
use PenguinPark\Monad\Maybe\Maybe;

$maybe = Maybe::fromNullable($_GET['id'] ?? null)
    ->map(fn($s) => trim($s))
    ->filter(fn($s) => $s !== '')
    ->flatMap(fn($s) => is_numeric($s) ? Maybe::just((int)$s) : Maybe::nothing());

$id = $maybe->getOrElse(0); // default when absent or invalid
```

2) Convert to Either for error messaging
```php
$either = $maybe->toEither('missing id');
```

3) Applicative combining
```php
$mkUser = fn($id, $name) => ['id'=>$id,'name'=>$name];
$idM   = Maybe::just(7);
$nameM = Maybe::just('Ada');
$u = Maybe::liftA2($mkUser, $idM, $nameM); // Just(['id'=>7,'name'=>'Ada'])
```
