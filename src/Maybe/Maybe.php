<?php /** @noinspection PhpUnused */

    /**
     * @codeCoverageIgnore
     */
    /**
     * Until I determine what is different from local env and CI env
     */


    declare(strict_types=1);

namespace PenguinPark\Monad\Maybe;

use PenguinPark\Monad\Exception\ContractViolation;
use PenguinPark\Monad\Either\Either;
use PenguinPark\Monad\Either\Left;
use PenguinPark\Monad\Either\Right;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Typeclass\Monad;
use Throwable;

/**
 * @template T
 */
abstract class Maybe implements Monad
{
    /**
     * Apply a wrapped function to a wrapped value.
     * Receiver ($this) must be Maybe<callable(A):B>, argument is Maybe<A>, result is Maybe<B>.
     *
     * @template A
     * @template B
     * @param Maybe|Apply $fa
     * @return Maybe<B>
     */
    abstract public function ap(Maybe|Apply $fa): Maybe;

    /** Structural equality (type + payload). */
    abstract public function equals(mixed $other): bool;

    /**
     * Keep value only if predicate holds (Just->Nothing on false).
     *
     * @param callable(T):bool $predicate
     * @return Maybe<T>
     */
    abstract public function filter(callable $predicate): Maybe;

    /**
     * @template U
     * @param callable(T):Maybe<U> $function
     * @return Maybe<U>
     */
    abstract public function flatMap(callable $function): Maybe;

    /**
     * Total deconstructor.
     *
     * @template R
     * @param callable():R      $onNothing
     * @param callable(T):R     $onJust
     * @return R
     */
    abstract public function fold(callable $onNothing, callable $onJust);

    /**
     * Construct a Maybe from an Either.
     * - Right(r) -> Just(r)
     * - Left(_)  -> Nothing
     *
     * @template L
     * @template R
     * @param Either<L,R> $e
     * @return Maybe  @phpstan-return Maybe<R>
     */
    public static function fromEither(Either $e): Maybe
    {
        return $e->fold(
            static fn ($l) => self::nothing(),
            static fn ($r) => self::just($r)
        );
    }

    /**
     * @template U
     * @param ?U $value
     * @return Maybe<U>
     */
    public static function fromNullable(mixed $value): Maybe
    {
        return $value === null
            ? new Nothing()
            : new Just($value);
    }

    /**
     * Null-safe constructor: null => Nothing, else Just($value).
     * Alias it as fromNullable() if you prefer that name.
     */
    public static function fromValue(mixed $value): self
    {
        return $value === null
            ? new Nothing()
            : new Just($value);
    }

    /**
     * Return contained value or a default (value or provider).
     *
     * @param mixed|callable():mixed $default
     * @return mixed
     */
    abstract public function getOrElse(mixed $default): mixed;

    /**
     * Return contained value or throw given/created throwable.
     *
     * @param Throwable|string|callable():Throwable $throwable
     * @return mixed
     */
    abstract public function getOrThrow(Throwable|string|callable $throwable): mixed;

    /**
     * Introspection.
     */
    abstract public function isJust(): bool;
    abstract public function isNothing(): bool;

    /**
     * @template U
     * @param U $value
     * @return Maybe<U>
     */
    public static function just(mixed $value): Maybe
    {
        return new Just($value);
    }

    /**
     * Applicative helper for 2-arg functions.
     *
     * @template A
     * @template B
     * @template C
     * @param callable(A,B):C $f
     * @param Maybe<A> $fa
     * @param Maybe<B> $fb
     * @return Maybe<C>
     */
    public static function liftA2(callable $f, Maybe $fa, Maybe $fb): Maybe
    {
        $curried = static fn ($a) => static fn ($b) => $f($a, $b);
        return self::pure($curried)->ap($fa)->ap($fb);
    }

    /**
     * @template U
     * @param callable $f
     * @return Maybe<U>
     */
    abstract public function map(callable $f): static;

    /**
     * @template U
     * @return Maybe<U>
     */
    public static function nothing(): Maybe
    {
        return new Nothing();
    }

    /**
     * Alias for just(); common in FP literature as `unit`/`pure`.
     * @template U
     * @param U $value
     * @return Maybe<U>
     */
    public static function of(mixed $value): static
    {
        return new Just($value);
    }

    /**
     * Replace Nothing with supplier(); pass-through for Just.
     *
     * @param callable():Maybe<T> $supplier
     * @return Maybe<T>
     */
    abstract public function orElse(callable $supplier): Maybe;

    /**
     * Return value or null (never throws).
     */
    abstract public function orNull(): mixed;

    /** Lift a raw value into Maybe (alias of just/of). */
    public static function pure(mixed $a): static
    {
        return self::just($a);
    }

    /**
     * Sequence an array of Maybe<T> into a Maybe<array<T>>.
     * Stops at the first Nothing.
     *
     * @template U
     * @param array<int, Maybe> $ms   @phpstan-param array<int, Maybe<U>> $ms
     * @return Maybe  @phpstan-return Maybe<array<int, U>>
     */
    public static function sequenceArray(array $ms): Maybe
    {
        $acc = [];
        foreach ($ms as $i => $m) {
            if (!$m instanceof self) {
                throw new ContractViolation("Maybe::sequenceArray expects elements of type Maybe at index $i");
            }
            if ($m->isNothing()) {
                return self::nothing();
            }
            $acc[] = $m->getOrElse(null); // Just(null) is valid
        }
        return self::just($acc);
    }

    /**
     * Side-effect hook; no change to structure.
     *
     * @param callable(T):void $effect
     * @return $this
     */
    abstract public function tap(callable $effect): self;

    /**
     * Convert this Maybe<T> into an Either<L, T>.
     * - Just(t)   -> Right(t)
     * - Nothing   -> Left(L), where L is provided as a raw value or no-arg supplier
     *
     * @template L
     * @param L|callable():L $ifNothing  Value or supplier used when this is Nothing
     * @return Either  @phpstan-return Either<L, T>
     */
    public function toEither(mixed $ifNothing): Either
    {
        return $this->fold(
            static function () use ($ifNothing): Either {
                $l = is_callable($ifNothing) ? $ifNothing() : $ifNothing;
                return new Left($l);
            },
            static fn ($t): Either => new Right($t)
        );
    }

    /**
     * Traverse an array with a function returning Maybe.
     * Equivalent to map then sequence, short-circuits on Nothing.
     *
     * @template A
     * @template B
     * @param array<int, A> $xs
     * @param callable(A):Maybe $f   @phpstan-param callable(A):Maybe<B> $f
     * @return Maybe  @phpstan-return Maybe<array<int, B>>
     */
    public static function traverseArray(array $xs, callable $f): Maybe
    {
        $acc = [];
        foreach ($xs as $i => $x) {
            $m = $f($x);
            if (!$m instanceof self) {
                throw new ContractViolation("Maybe::traverseArray fn must return Maybe at index $i");
            }
            if ($m->isNothing()) {
                return self::nothing();
            }
            $acc[] = $m->getOrElse(null);
        }
        return self::just($acc);
    }

}
