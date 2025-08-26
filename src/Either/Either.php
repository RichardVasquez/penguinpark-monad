<?php /** @noinspection PhpUnused */

declare(strict_types=1);

namespace PenguinPark\Monad\Either;

use PenguinPark\Monad\Maybe\Maybe;
use PenguinPark\Monad\Typeclass\Apply;
use PenguinPark\Monad\Typeclass\Monad;
use Throwable;

/**
 * Disjoint union type representing a value of one of two possible types (a sum type).
 * Left is conventionally used for the error/alternative path, Right for the success path.
 *
 * @template L Type of the Left value
 * @template R Type of the Right value
 */
abstract class Either implements Monad
{

    /**
     * Apply a wrapped function to a wrapped value (right-biased).
     * Receiver is Either<L, callable(A):B>, argument is Either<L, A>, result is Either<L, B>.
     *
     * @template A
     * @template B
     * @param Either|Apply $fa
     * @return Either
     */
    abstract public function ap(self|Apply $fa): Either;

    /**
     * Side-effect hook for compatibility with a Monad-like interface.
     * Should not change the underlying structure; commonly returns $this.
     *
     * @param callable(R|L):void $function Function receiving the contained value when applicable
     * @return static
     */
    abstract public function apply(callable $function): Either;

    /**
     * Map both sides at once.
     *
     * @template U
     * @template V
     * @param callable(L):U $fl Mapper for the Left value
     * @param callable(R):V $fr Mapper for the Right value
     * @return Either<U,V>
     */
    public function bimap(callable $fl, callable $fr): Either
    {
        return $this->fold(
            fn ($l) => new Left($fl($l)),
            fn ($r) => new Right($fr($r))
        );
    }

    /**
     * Right-biased bind (Haskell >>=). Alias of flatMap.
     *
     * @template B
     * @param callable $function
     * @return Either
     */
    public function bind(callable $function): static
    {
        return $this->flatMap($function);
    }

    /** Structural equality (branch + payload). */
    abstract public function equals(mixed $other): bool;

    /**
     * Chain computations that may also produce an Either.
     * Applies the function to the Right value and expects an Either in response;
     * Left is passed through unchanged.
     *
     * @template U
     * @param callable $function
     * @return Either<L,U>
     */
    abstract public function flatMap(callable $function): Either;

    /**
     * Total deconstructor for Either.
     *
     * @param callable(L):mixed $onLeft  Handler invoked if this is Left
     * @param callable(R):mixed $onRight Handler invoked if this is Right
     * @return mixed Result of the executed handler
     */
    abstract public function fold(callable $onLeft, callable $onRight): mixed;

    /**
     * Unsafe unwrap for compatibility with a Monad-like interface.
     * Implementations may throw when unwrapping the Left case.
     *
     * @return mixed
     */
    abstract public function get(): mixed;

    /**
     * Deprecated: Historical projection method; now returns Maybe of the Left value.
     * Prefer toMaybeLeft(): Maybe<L>.
     *
     * @deprecated Use toMaybeLeft(): Maybe<L>
     * @return Maybe  @phpstan-return Maybe<L>
     */
    abstract public function getLeft(): Maybe;

    /**
     * Convenience: get the Left value or a default (value or supplier).
     *
     * @param mixed|callable():mixed $default
     */
    public function getLeftOrElse(mixed $default): mixed
    {
        return $this->fold(
            static fn ($l) => $l,
            static fn ($r) => is_callable($default) ? $default() : $default
        );
    }

    /**
     * Extract the Right value if present, otherwise return or compute a default.
     *
     * @param mixed|callable():mixed $default A raw default value or a no-arg supplier to be invoked lazily
     * @return mixed The contained Right value or the default
     */
    abstract public function getOrElse(mixed $default): mixed;

    /**
     * Deprecated: Historical projection method; now returns Maybe of the Right value.
     * Prefer toMaybeRight(): Maybe<R>.
     *
     * @deprecated Use toMaybeRight(): Maybe<R>
     * @return Maybe  @phpstan-return Maybe<R>
     */
    abstract public function getRight(): Maybe;

    /**
     * Convenience: get the Right value or a default (value or supplier).
     *
     * @param mixed|callable():mixed $default
     */
    public function getRightOrElse(mixed $default): mixed
    {
        return $this->fold(
            static fn ($l) => is_callable($default) ? $default() : $default,
            static fn ($r) => $r
        );
    }

    /**
     * @return bool True if this is a Left
     */
    abstract public function isLeft(): bool;

    /**
     * @return bool True if this is a Right
     */
    abstract public function isRight(): bool;

    /** Construct a Left value. */
    public static function left(mixed $value): self
    {
        return new Left($value);
    }

    /**
     * Convenience: get the Left value or null.
     */
    public function leftOrNull(): mixed
    {
        return $this->fold(
            static fn ($l) => $l,
            static fn ($r) => null
        );
    }

    /**
     * Applicative helper for 2-arg functions.
     *
     * @template A
     * @template B
     * @template C
     * @param callable(A,B):C $f
     * @param Either<L,A> $fa
     * @param Either<L,B> $fb
     * @return Either<L,C>
     */
    public static function liftA2(callable $f, Either $fa, Either $fb): Either
    {
        $curried = static fn ($a) => static fn ($b) => $f($a, $b);
        return self::pure($curried)->ap($fa)->ap($fb);
    }

    /**
     * Transform the Right value using the given function; Left is passed through unchanged.
     *
     * @template U
     * @param callable(R):U $f Mapping function applied when this is Right
     * @return Either<L,U> Right with mapped value, or the original Left
     */
    abstract public function map(callable $f): static|self;

    /**
     * Transform the Left value using the given function; Right is passed through unchanged.
     *
     * @template M
     * @param callable(L):M $f Mapping function applied when this is Left
     * @return Either<M,R>
     */
    abstract public function mapLeft(callable $f): Either;

    public static function of(mixed $value): static|self   { return self::right($value); }

    /**
     * Replace a Left with the result of supplier(); pass-through for Right.
     *
     * @param callable():Either<L,R> $supplier Supplier invoked only when this is Left
     * @return Either<L,R>
     */
    abstract public function orElse(callable $supplier): Either;

    /**
     * Unwrap the Right value or throw an exception mapped from Left.
     *
     * @param callable(mixed):Throwable $mapLeftToException
     * @return mixed
     */
    abstract public function orThrow(callable $mapLeftToException): mixed;

    /** Lift a raw value into Either as Right (alias of right). */
    public static function pure(mixed $a): static
    {
        return new Right($a);
    }

    /** Construct a Right value. */
    public static function right(mixed $value): self
    {
        return new Right($value);
    }

    /**
     * Convenience: get the Right value or null.
     */
    public function rightOrNull(): mixed
    {
        return $this->fold(
            static fn ($l) => null,
            static fn ($r) => $r
        );
    }

    /**
     * Swap Left and Right values.
     *
     * @return Either<R,L>
     */
    abstract public function swap(): Either;

    /**
     * Side-effect on Right only; return self unchanged.
     * @param callable(mixed):void $effect
     * @return $this
     */
    abstract public function tap(callable $effect): self;

    /**
     * Side-effect on Left only; return self unchanged.
     * @param callable(mixed):void $effect
     * @return $this
     */
    abstract public function tapLeft(callable $effect): self;

    /**
     * Project the Left value to Maybe.
     * Left(l) -> Just(l), Right(_) -> Nothing
     *
     * @template LL
     * @return Maybe  @phpstan-return Maybe<L>
     */
    public function toMaybeLeft(): Maybe
    {
        return $this->fold(
            static fn ($l) => Maybe::just($l),
            static fn ($r) => Maybe::nothing()
        );
    }

    /**
     * Project the Right value to Maybe.
     * Right(r) -> Just(r), Left(_) -> Nothing
     *
     * @template RR
     * @return Maybe  @phpstan-return Maybe<R>
     */
    public function toMaybeRight(): Maybe
    {
        return $this->fold(
            static fn ($l) => Maybe::nothing(),
            static fn ($r) => Maybe::just($r)
        );
    }

    // Optional ergonomic alias if you insist on the shorter name:
    /** @see self::tryCatch() */
    public static function try(callable $thunk, ?callable $mapThrowable = null): self
    {
        return self::tryCatch($thunk, $mapThrowable);
    }

    /**
     * Execute a thunk. Success -> Right($value). Throwable -> Left($mapped).
     *
     * @param callable():R $thunk
     * @param null|callable(Throwable):L $mapThrowable  Map the thrown \Throwable to a Left value.
     *                                                  If null, the Throwable itself is placed in Left.
     * @return Either
     * @phpstan-return Either<L|Throwable, R>
     */
    public static function tryCatch(callable $thunk, ?callable $mapThrowable = null): self
    {
        try {
            /** @var R $r */
            $r = $thunk();
            return new Right($r);
        } catch (Throwable $e) {
            $left = $mapThrowable ? $mapThrowable($e) : $e;
            return new Left($left);
        }
    }

    public static function unit(mixed $value): self { return self::right($value); }
}
