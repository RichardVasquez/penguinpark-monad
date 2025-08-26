<?php declare(strict_types=1);

namespace PenguinPark\Monad\Validation;

use PenguinPark\Monad\Typeclass\Applicative;
use PenguinPark\Monad\Typeclass\Apply;

/**
 * @template E of array<int, mixed>  Error collection (Semigroup via array_merge)
 * @template A                        Success value type
 */
abstract class Validation implements Applicative
{
    /**
     * Applicative apply: if this holds a function and $vb holds a value,
     * apply and accumulate errors from either side.
     *
     * @template B
     * @param Validation|Apply $fa
     * @return Validation<E, mixed>
     */
    abstract public function ap(self|Apply $fa): self;

    /**
     * Deconstruct.
     * @template R
     * @param callable(E):R $onInvalid
     * @param callable(A):R $onValid
     * @return mixed
     */
    abstract public function fold(callable $onInvalid, callable $onValid): mixed;

    /**
     * Extract with default when invalid.
     * @param A|callable(E):A $default
     * @return mixed
     */
    public function getOrElse(mixed $default): mixed
    {
        return $this->fold(
            fn (array $errs) => is_callable($default) ? $default($errs) : $default,
            fn (mixed $a) => $a
        );
    }

    /**
     * Convenience: build an Invalid from one or many errors.
     * @param array<int, mixed>|mixed $err
     * @return Invalid
     */
    public static function invalid(mixed $err): Invalid
    {
        return new Invalid(is_array($err) ? $err : [$err]);
    }

    /** @return bool */
    public function isInvalid(): bool
    {
        return !$this->isValid();
    }

    /** @return bool */
    abstract public function isValid(): bool;

    /**
     * Map over the success value.
     * @template B
     * @param callable(A):B $f
     * @return Validation<E, B>
     */
    abstract public function map(callable $f): self;

    /**
     * Lift a pure value.
     * @template T
     * @param T $value
     * @return Valid<array<int, mixed>, T>
     */
    public static function of(mixed $value): Valid
    {
        /** @var Valid<array<int, mixed>, mixed> $v */
        $v = new Valid($value);
        return $v;
    }
}
