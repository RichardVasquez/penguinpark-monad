<?php /** @noinspection PhpUnused */

declare(strict_types=1);

namespace PenguinPark\Monad\ListM;

use IteratorAggregate;
use PenguinPark\Monad\Exception\ApCallableExpected;
use PenguinPark\Monad\Exception\ContractViolation;
use PenguinPark\Monad\Either\Either;
use PenguinPark\Monad\Exception\InvalidBindReturnType;
use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Typeclass\Monad;
use PenguinPark\Monad\Util\Eq;
use Traversable;

/**
 * Immutable list monad with basic functional operations (map, flatMap, filter, folds, etc.).
 *
 * @template T Element type
 */
final class ListM implements IteratorAggregate, Monad
{
    /** @var array<int, T> */
    private array $values;

    /**
     * @param array<int, T> $values
     */
    private function __construct(array $values)
    {
        // reindex for predictability
        $this->values = array_values($values);
    }

    public function __toString(): string
    {
        return 'ListM(' . implode(', ', array_map(
                fn($v) => is_object($v)
                    ? get_class($v)
                    : var_export($v, true),
                $this->values
            )) . ')';
    }

    /**
     * Cartesian apply: ListM<callable(A):B> <*> ListM<A> => ListM<B>
     *
     * @template A
     * @template B
     * @param ListM|Apply $fa
     * @return ListM<B>
     */
    public function ap(self|Apply $fa): self
    {
        $out = [];
        foreach ($this->values as $f) {
            if (!is_callable($f)) {
                throw new ApCallableExpected('ListM::ap expects the receiver to hold callables');
            }
            foreach ($fa->values as $a) {
                $out[] = $f($a);
            }
        }
        return new self($out);
    }

    /**
     * @param ListM<T> $other
     * @return ListM<T>
     */
    public function append(self $other): self
    {
        // Value law only: result equals concatenation; no identity guarantee.
        return new self(array_merge($this->values, $other->values));
    }

    /**
     * Side-effect only; invokes function for each element and returns self for chaining.
     *
     * @param callable(T):void $function
     * @return $this
     */
    public function apply(callable $function): self
    {
        foreach ($this->values as $v) {
            $function($v);
        }
        return $this;
    }

    /**
     * Legacy alias for flatMap (Haskell bind ≈ >>=)
     * @template U
     * @param callable $function
     * @return self
     */
    public function bind(callable $function): self
    {
        return $this->flatMap($function);
    }

    /**
     * @param iterable<ListM<T>> $lists
     * @return ListM<T>
     */
    public function concat(iterable $lists): self
    {
        $out = $this->values;
        foreach ($lists as $l) {
            if (!$l instanceof self) {
                throw new ContractViolation('ListM::concat expects iterable of ListM');
            }
            if (!$l->isEmpty()) {
                array_push($out, ...$l->values);
            }
        }
        return new self($out);
    }

    /**
     * @return int Number of elements in the list
     */
    public function count(): int { return count($this->values); }

    /**
     * @template U
     * @return ListM<U>
     */
    public static function empty(): self
    {
        return new self([]);
    }

    /** Structural equality: same length, pairwise valueEq. */
    public function equals(mixed $other): bool
    {
        if (!$other instanceof self) {
            return false;
        }
        $a = $this->values;
        $b = $other->values;
        if (count($a) !== count($b)) {
            return false;
        }
        for ($i = 0, $n = count($a); $i < $n; $i++) {
            if (!Eq::valueEq($a[$i], $b[$i])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Custom element equality, e.g., approximate floats or case-insensitive strings.
     * @param ListM<T> $other
     * @param callable(mixed,mixed):bool $elemEq
     */
    public function equalsWith(self $other, callable $elemEq): bool
    {
        if (count($this->values) !== count($other->values)) {
            return false;
        }
        for ($i = 0, $n = count($this->values); $i < $n; $i++) {
            if (!$elemEq($this->values[$i], $other->values[$i])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param callable(T):bool $p
     * @return ListM<T>
     */
    public function filter(callable $p): self
    {
        $out = [];
        foreach ($this->values as $v) {
            if ($p($v)) {
                $out[] = $v;
            }
        }
        return new self($out);
    }

    /**
     * @template U
     * @param callable $function
     * @return ListM<U>
     */
    public function flatMap(callable $function): self
    {
        $out = [];
        foreach ($this->values as $v) {
            $m = $function($v);
            if (!$m instanceof self) {
                throw new InvalidBindReturnType('ListM::flatMap function must return ListM');
            }
            foreach ($m->values as $u) {
                $out[] = $u;
            }
        }
        return new self($out);
    }

    /**
     * @template A
     * @param A $init
     * @param callable(A,T):A $f
     * @return mixed
     */
    public function foldLeft(mixed $init, callable $f): mixed
    {
        $acc = $init;
        foreach ($this->values as $v) {
            $acc = $f($acc, $v);
        }
        return $acc;
    }

    /**
     * @template A
     * @param A $init
     * @param callable(T,A):A $f
     * @return mixed
     */
    public function foldRight(mixed $init, callable $f): mixed
    {
        $acc = $init;
        for ($i = count($this->values) - 1; $i >= 0; $i--) {
            $acc = $f($this->values[$i], $acc);
        }
        return $acc;
    }

    /**
     * @template U
     * @param array<int, U> $values
     * @return ListM<U>
     */
    public static function fromArray(array $values): self
    {
        return new self($values);
    }

    /**
     * Unwrap as raw array.
     *
     * @return array<int,T>
     */
    public function get(): array { return $this->values; }

    /**
     * @return Traversable<int,T> Iterator over the elements in order
     */
    public function getIterator(): Traversable
    {
        foreach ($this->values as $v) {
            yield $v;
        }
    }

    /**
     * Return the first element if present.
     *
     * @return Maybe<T> Just(head) or Nothing if empty
     */
    public function head():Maybe
    {
        // call instance method, not statically
        return $this->headOption();
    }

    /**
     * Optionally return the first element.
     *
     * @return Maybe<T> Just(first) or Nothing if empty
     */
    public function headOption(): Maybe
    {
        return $this->isEmpty()
            ? Maybe::nothing()
            : Maybe::just($this->values[0]);
    }

    /**
     * @return bool True if the list contains no elements
     */
    public function isEmpty(): bool
    {
        return $this->values === [];
    }

    /**
     * Return the last element if present.
     *
     * @return Maybe<T> Just(last) or Nothing if empty
     */
    public function last():Maybe
    {
        // call instance method, not statically
        return $this->lastOption();
    }

    /**
     * Optionally return the last element.
     *
     * @return Maybe<T> Just(last) or Nothing if empty
     */
    public function lastOption(): Maybe
    {
        if ($this->isEmpty()) {
            return Maybe::nothing();
        }
        $last = $this->values[count($this->values) - 1];
        return Maybe::just($last);
    }

    /**
     * Applicative helper for 2-arg functions (cartesian).
     *
     * @template A
     * @template B
     * @template C
     * @param callable(A,B):C $f
     * @param ListM<A> $fa
     * @param ListM<B> $fb
     * @return ListM<C>
     */
    public static function liftA2(callable $f, ListM $fa, ListM $fb): self
    {
        $curried = static fn ($a) => static fn ($b) => $f($a, $b);
        return self::pure($curried)->ap($fa)->ap($fb);
    }

    /**
     * @template U
     * @param callable(T):U $f
     * @return ListM<U>
     */
    public function map(callable $f): ListM
    {
        $out = [];
        foreach ($this->values as $v) {
            $out[] = $f($v);
        }
        return new self($out);
    }

    /**
     * @template U
     * @param U ...$values
     * @return ListM<U>
     */
    public static function of(...$values): self
    {
        return new self($values);
    }

    /** Applicative alias */
    public static function pure(mixed $a): self
    {
        return self::of($a);
    }

    /**
     * Left fold (like array_reduce): reducer(acc, value) -> acc'
     * @template A
     * @param callable(A, mixed): A $combine
     * @param A $initial
     * @return mixed
     */
    public function reduce(callable $combine, mixed $initial): mixed
    {
        $acc = $initial;
        foreach ($this->values as $v) {
            $acc = $combine($acc, $v);
        }
        return $acc;
    }

    /**
     * Non-empty left fold (uses first element as seed). Throws on empty.
     * @param callable(mixed, mixed): mixed $combine
     */
    public function reduce1(callable $combine): mixed
    {
        if (empty($this->values)) {
            throw new ContractViolation('ListM::reduce1 called on empty list');
        }
        $acc = $this->values[0];
        for ($i = 1, $n = count($this->values); $i < $n; $i++) {
            $acc = $combine($acc, $this->values[$i]);
        }
        return $acc;
    }

    public function reduceL(mixed $initial, callable $combine): mixed
    {
        return $this->reduce($combine, $initial);
    }

    /**
     * Right fold: reducer(value, acc) -> acc'
     * @template A
     * @param callable(mixed, A): A $combine
     * @param A $initial
     * @return mixed
     */
    public function reduceRight(callable $combine, mixed $initial): mixed
    {
        $acc = $initial;
        for ($i = count($this->values) - 1; $i >= 0; $i--) {
            $acc = $combine($this->values[$i], $acc);
        }
        return $acc;
    }

    /**
     * Sequence a ListM<Either<L,T>> into Either<L, ListM<T>>.
     *
     * @return Either
     */
    public function sequenceEither(): Either
    {
        $acc = [];
        foreach ($this->values as $i => $e) {
            if (!$e instanceof Either) {
                throw new ContractViolation("ListM::sequenceEither expects elements of type Either at index $i");
            }
            if ($e->isLeft()) {
                // preserve original Left payload
                return $e;
            }
            $acc[] = $e->getOrElse(null);
        }
        return Either::right(self::fromArray($acc));
    }

    /**
     * Sequence a ListM<Maybe<T>> into Maybe<ListM<T>>.
     */
    public function sequenceMaybe(): Maybe
    {
        $acc = [];
        foreach ($this->values as $i => $m) {
            if (!$m instanceof Maybe) {
                throw new ContractViolation("ListM::sequenceMaybe expects elements of type Maybe at index $i");
            }
            if ($m->isNothing()) {
                return Maybe::nothing();
            }
            $acc[] = $m->getOrElse(null); // Just(null) is valid
        }
        return Maybe::just(self::fromArray($acc));
    }

    /**
     * Return the tail of the list (all elements except the first) if non-empty.
     *
     * @return Maybe<ListM<T>> Just(ListM of remaining elements) or Nothing if empty
     */
    public function tail():Maybe
    {
        // Return the list without its head: Nothing for empty; Just(ListM(values[1..])) otherwise
        if ($this->isEmpty()) {
            return Maybe::nothing();
        }
        // slice from index 1 to end; preserve element order
        $rest = array_slice($this->values, 1);
        return Maybe::just(self::fromArray($rest));
    }

    /**
     * @return array<int,T> Copy of the underlying array
     */
    public function toArray(): array
    {
        return $this->values;
    }

    /**
     * Traverse this ListM<T> using fn(T): Either<L,U> into Either<L, ListM<U>>.
     * Short-circuits on first Left.
     *
     * @param callable(mixed):Either $f
     * @return Either
     */
    public function traverseEither(callable $f): Either
    {
        $acc = [];
        foreach ($this->values as $i => $v) {
            $e = $f($v);
            if (!$e instanceof Either) {
                throw new ContractViolation("ListM::traverseEither fn must return Either at index $i");
            }
            if ($e->isLeft()) {
                return $e;
            }
            $acc[] = $e->getOrElse(null);
        }
        return Either::right(self::fromArray($acc));
    }

    /**
     * Traverse this ListM<T> using fn(T): Maybe<U> into Maybe<ListM<U>>.
     * Stops at first Nothing.
     *
     * @param callable(mixed):Maybe $f
     * @return Maybe
     */
    public function traverseMaybe(callable $f): Maybe
    {
        $acc = [];
        foreach ($this->values as $i => $v) {
            $m = $f($v);
            if (!$m instanceof Maybe) {
                throw new ContractViolation("ListM::traverseMaybe fn must return Maybe at index $i");
            }
            if ($m->isNothing()) {
                return Maybe::nothing();
            }
            $acc[] = $m->getOrElse(null);
        }
        return Maybe::just(self::fromArray($acc));
    }

    /** Legacy/ergonomic alias if your codegen asked for it */
    public static function unit(mixed $value): self
    {
        return self::of($value);
    }

    /**
     * Pairwise zip with a combining function (min length wins).
     *
     * @template A
     * @template B
     * @template C
     * @param callable(A,B):C $f
     * @param ListM<B> $other
     * @return ListM<C>
     */
    public function zipWith(callable $f, ListM $other): self
    {
        $n = min(count($this->values), count($other->values));
        $out = [];
        for ($i = 0; $i < $n; $i++) {
            $out[] = $f($this->values[$i], $other->values[$i]);
        }
        return new self($out);
    }
}
